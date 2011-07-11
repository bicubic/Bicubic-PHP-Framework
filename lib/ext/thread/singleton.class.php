<?php

/**
  Copyright (c) 2009, Jamie Estep (jestep.com | ecommerce-blog.org)
  All rights reserved.

  Redistribution and use in source and binary forms, with or without
  modification, are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright
  notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright
  notice, this list of conditions and the following disclaimer in the
  documentation and/or other materials provided with the distribution.
 * Neither the name of the <organization> nor the
  names of its contributors may be used to endorse or promote products
  derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY Jamie Estep ''AS IS'' AND ANY
  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL Jamie Estep BE LIABLE FOR ANY
  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Create a singleton process tracker
 */
class TrackPid {

    private static $instance;
    private $data;

    public $error;

    private function __construct($application) {
        $this->data = new MySQLData(
                        $application->config['mysql_host'],
                        $application->config['mysql_user'],
                        $application->config['mysql_password'],
                        $application->config['mysql_database']
        );
    }

    public static function singleton($application) {
        if (!self::$instance) {
            self::$instance = new trackPid($application);
        }
        return self::$instance;
    }

    /**
     * Create a unique envelope so that this script could potentially run in parallel with another copy of it
     */
    function createEnvelope($processes, $timeout=60) {
        $envelope = self::randomName();
        for ($i = 1; $i <= $processes; $i++) {
            $daemon = new PublicationDaemon();
            $daemon->envelope = $envelope;
            $daemon->pid = $i;
            $daemon->id = $this->data->insert($daemon);
            if (!isset($daemon->id)) {
                return false;
            }
        }
        return $envelope;
    }

    /**
     * Set a specific PID as complete
     */
    function setPidComplete($envelope, $pid) {
        $this->data->begin();
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        $daemon->pid = $pid;
        $daemon = $this->data->selectOne($daemon);
        if (!isset($daemon->id)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $daemon->status = 1;
        if (!$this->data->update($daemon)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $this->data->commit();
    }

    /**
     * Set the output of a specific PID if you need to retrieve it on the main script
     */
    function setOutput($envelope, $pid, $output) {
        $this->data->begin();
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        $daemon->pid = $pid;
        $daemon = $this->data->selectOne($daemon);
        if (!isset($daemon->id)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $daemon->output = $output;
        if (!$this->data->update($daemon)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $this->data->commit();
    }

    function setInput($envelope, $pid, $input) {
        $this->data->begin();
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        $daemon->pid = $pid;
        $daemon = $this->data->selectOne($daemon);
        if (!isset($daemon->id)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $daemon->input = $input;
        if (!$this->data->update($daemon)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $this->data->commit();
    }

    /**
     * Return the output for a particular PID
     */
    function returnOutput($envelope, $pid) {
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        $daemon->pid = $pid;
        $daemon = $this->data->selectOne($daemon);
        if (!isset($daemon->id)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        return $daemon->output;
    }

    function isOpen($envelope, $pid) {
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        $daemon->pid = $pid;
        $daemon->status = 0;
        $daemon = $this->data->selectOne($daemon);
        if (!isset($daemon->id)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
             return false;
        }
        return true;
    }

    /**
     * Check to see if all the PIDS are finished processing, fail if any are still processing
     */
    function returnStatus($envelope) {
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        $daemons = $this->data->select($daemon);
        foreach($daemons as $daemon) {
            if ($daemon->status == 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Manually destroy everything
     */
    function cleanup($envelope) {
        $this->data->begin();
        $daemon = new PublicationDaemon();
        $daemon->envelope = $envelope;
        if (!$this->data->delete($daemon)) {
             $this->data->rollback();
             $this->error = $this->data->getError();
        }
        $this->data->commit();
    }

    /**
     * Checks the status of our processes. Once they are successful returns SUCCESS, otherwise returns as failed.
     */
    function checkStratus($envelope, $timeout = 10, $sleep = 100000) {
        $cur = 0;
        while ($cur < $timeout) {
            // wait for 100000 microseconds, or .1 seconds by default
            usleep($sleep);
            //usleep is microseconds, and we need to convert it to seconds
            $cur += ( $sleep / 1000000);
            if ($this->returnStatus($envelope)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate a random string for our envelope
     */
    static function randomName() {
        $string = '';
        $possible = "0123456789bcdfghjkmnpqrstvwxyzABTOIIUYBHXSB";
        for ($i = 1; $i < 16; $i++):
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $string .= $char;
        endfor;
        return $string;
    }

}