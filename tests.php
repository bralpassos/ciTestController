<?php
/**
 * tests.php
 *
 * This is an improvement for CodeIgniter's UnitTest Library.
 * This helps you writing more organized tests, separating them into methods.
 * By default it only shows failed tests.
 *
 * I want to keep this software in a single controller file. But if grows
 * bigger I'll think a way of splitting it into a controller and a view.
 *
 * @author Evaldo Junior <junior@casoft.info>
 * @version 0.2
 * @package
 * @subpackage controllers
 * @license The BSD License
 *
 * ==== Changelog ====
 * Version 0.1 - June 11th 2011
 *     - First release
 *     - Tests' results are shown in a table
 *     - Write tests into methods
 *
 * Version 0.2 - June 12th 2011
 *     - Added a title to the report
 *     - Added lots of comments/doc to code
 *     - Added some style to report table 
 *
 * == The BSD License ==
 *
 * Copyright (c) 2011, Evaldo Junior Bento <junior@casoft.info>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation and/or
 *   other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Tests class.
 *
 * Write tests adding methods named _test_TEST_NAME()
 *
 * @property CI_Loader  $load
 * @property CI_Input   $input
 */
class Tests extends CI_Controller { 
    
    /** 
     * Construtor Method
     */
    public function  __construct() { 
        parent::__construct();

        /**
         * CONFIG YOUR SETTINGS HERE
         */
        $this->conf = array(
            'show_passed'       => FALSE,           // Show passed tests?
            'app_name'          => 'CodeIgniter'    // Your app's name, show in report's header
        );
    }

    /**
     * index
     *
     * Default method
     * 
     * @access public
     * @return void
     */
    public function index() {
        // Running all tests...
        $this->load->library('unit_test');
        $this->_run_all_tests();
        $this->_display_results();
    }

    /**
     * _run_all_tests
     *
     * This method run all methods name _test_*()
     * 
     * @access private
     * @return void
     */
    private function _run_all_tests() {
        $methods = get_class_methods('Tests');

        // Getting methods' names
        foreach ($methods as $method) {
            if (strpos($method, '_test') === 0) {
                $this->$method();
            }
        }
 
        // CI UnitTest
        $results = $this->unit->result();

        $this->total_tests  = 0;
        $this->passed_tests = 0;
        $this->failed_tests = 0;

        // TODO split the template to a view?
        $style_passed   = 'color: #0C0; font-size: 140%; font-weight: bold; text-align: center;';
        $style_failed   = 'color: #C00; font-size: 140%; font-weight: bold; text-align: center;';
        $first_line     = 'style="border: 1px solid #444;"';
        $lines          = 'style="border: 1px solid #444; border-top: 0px;"';

        $this->result_table = '<table style="width: 100%; font-size: 12px; color: #444;">';

        foreach ($results as $result) {
            $this->total_tests++;
            if ($result['Result'] == 'Passed') {
                $this->passed_tests++;
                if ($this->conf['show_passed']) {
                    $this->result_table .= '<tr '.$first_line.'><td colspan="2" style="'.$style_passed.'">'.$result['Test Name'].'</td></tr>';
                    $this->result_table .= '<tr><td style="width: 150px;">Test Datatype</td><td>'.$result['Test Datatype'].'</td></tr>';
                    $this->result_table .= '<tr><td>Expected Datatype</td><td>'.$result['Expected Datatype'].'</td></tr>';
                    $this->result_table .= '<tr><td>Test Result</td><td style="color: #0c0;">Passed</td></tr>';
                    $this->result_table .= '<tr><td>Line Number</td><td>'.$result['Line Number'].'</td></tr>';
                    $this->result_table .= '<tr><td>Notes</td><td>'.$result['Notes'].'</td></tr>';
                    $this->result_table .= '<tr><td colspan="2">&nbsp;</td></tr>';
                }
            }
            else {
                $this->failed_tests++;
                $this->result_table .= '<tr><td colspan="2" style="'.$style_failed.'">'.$result['Test Name'].'</td></tr>';
                $this->result_table .= '<tr><td style="width: 150px;">Test Datatype</td><td>'.$result['Test Datatype'].'</td></tr>';
                $this->result_table .= '<tr><td>Expected Datatype</td><td>'.$result['Expected Datatype'].'</td></tr>';
                $this->result_table .= '<tr><td>Test Result</td><td style="color: #c00;">Failed</td></tr>';
                $this->result_table .= '<tr><td>Line Number</td><td>'.$result['Line Number'].'</td></tr>';
                $this->result_table .= '<tr><td>Notes</td><td>'.$result['Notes'].'</td></tr>';
                $this->result_table .= '<tr><td colspan="2">&nbsp;</td></tr>';
            }
        }

        $this->result_table .= '</table>';
    }

    /**
     * _display_results
     *
     * This method writes the output to the browser
     * 
     * @access private
     * @return void
     */
    private function _display_results() {
        // CI HTML helper
        $this->load->helper('html');

        // Background color for the result div. Green for all tests passed and red if one or more failed.
        $div_color = ($this->failed_tests > 0) ? '#C00' : '#0C0';
        
        echo doctype();
        echo '<html><head>';
        echo meta('Content-type', 'text/html; charset=utf-8', 'equiv');
        echo "<title>Tests for '{$this->conf['app_name']}'</title>";
        echo '</head><body>';
        echo '<div style="width: 700px; margin: 10px auto;">';
        echo heading("UnitTests for {$this->conf['app_name']}", 1);
        echo '<div style="padding: 1px 10px; background-color: '.$div_color.'; margin-bottom: 20px;">';
        echo '<h2>Tests : '.$this->total_tests.'</h2>';
        echo '<h2>Fails : '.$this->failed_tests.'</h2>';
        echo '</div>';
        echo $this->result_table;
        echo '</div></body>';

    }

    /*************************************************
     * Write your tests below
     *
     * Your methods must be named _test_TEST_NAME()
     * and also must be private
     * 
     * Here are two test examples
     ************************************************/
    private function _test_example_passes() {
        // This test will pass
        $this->unit->run(2, 2, 'Two equal two =)');
    }

    private function _test_example_fails() {
        // This test will NOT pass
        $this->unit->run(4, 2, 'Four equal two??');
    }
}

/* End of file tests.php */
/* Location: ./application/controllers/tests.php */
