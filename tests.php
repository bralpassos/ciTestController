<?php
/**
 * tests.php
 *
 * This is an improvement for CodeIgniter's UnitTest Library.
 * This helps you writing more organized tests. By default it only shows fail tests.
 *
 * @author Evaldo Junior <junior@casoft.info>
 * @version 0.1
 * @package
 * @subpackage controllers
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

        $this->conf = array(
            'show_passed'       => FALSE,           // Show passed tests?
            'app_name'          => 'CodeIgniter'    // Your app's name
        );
    }

    public function index() {
        // Running all tests...
        $this->load->library('unit_test');
        $this->_run_all_tests();
        $this->_display_results();
    }

    private function _run_all_tests() {
        $methods = get_class_methods('Tests');

        foreach ($methods as $method) {
            if (strpos($method, '_test') === 0) {
                $this->$method();
            }
        }

        $results = $this->unit->result();

        $this->total_tests  = 0;
        $this->passed_tests = 0;
        $this->failed_tests = 0;

        $this->result_table = '<table style="width: 100%;">';

        foreach ($results as $result) {
            $this->total_tests++;
            if ($result['Result'] == 'Passed') {
                $this->passed_tests++;
                if ($this->conf['show_passed']) {
                    $this->result_table .= '<tr><td colspan="2" style="color: #0C0; font-size: 140%; font-weight: bold; text-align: center">'.$result['Test Name'].'</td></tr>';
                    $this->result_table .= '<tr><td style="width: 200px;">Test Datatype</td><td>'.$result['Test Datatype'].'</td></tr>';
                    $this->result_table .= '<tr><td>Expected Datatype</td><td>'.$result['Expected Datatype'].'</td></tr>';
                    $this->result_table .= '<tr><td>Test Result</td><td style="color: #0c0;">Passed</td></tr>';
                    $this->result_table .= '<tr><td>Line Number</td><td>'.$result['Line Number'].'</td></tr>';
                    $this->result_table .= '<tr><td>Notes</td><td>'.$result['Notes'].'</td></tr>';
                    $this->result_table .= '<tr><td colspan="2">&nbsp;</td></tr>';
                }
            }
            else {
                $this->failed_tests++;
                $this->result_table .= '<tr><td colspan="2" style="color: #C00; font-size: 140%; font-weight: bold; text-align: center">'.$result['Test Name'].'</td></tr>';
                $this->result_table .= '<tr><td style="width: 200px;">Test Datatype</td><td>'.$result['Test Datatype'].'</td></tr>';
                $this->result_table .= '<tr><td>Expected Datatype</td><td>'.$result['Expected Datatype'].'</td></tr>';
                $this->result_table .= '<tr><td>Test Result</td><td style="color: #c00;">Failed</td></tr>';
                $this->result_table .= '<tr><td>Line Number</td><td>'.$result['Line Number'].'</td></tr>';
                $this->result_table .= '<tr><td>Notes</td><td>'.$result['Notes'].'</td></tr>';
                $this->result_table .= '<tr><td colspan="2">&nbsp;</td></tr>';
            }
        }

        $this->result_table .= '</table>';
    }

    private function _display_results() {
        $this->load->helper('html');

        $div_color = ($this->failed_tests > 0) ? '#C00' : '#0C0';
        
        echo doctype();
        echo '<html><head>';
        echo meta('Content-type', 'text/html; charset=utf-8', 'equiv');
        echo "<title>Tests for '{$this->conf['app_name']}'</title>";
        echo '</head><body>';
        echo '<div style="width: 700px; margin: 10px auto;">';
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
     * Here is an test example
     ************************************************/
    private function _test_example_passes() {
        // This test will pass
        $this->unit->run(2, 2, 'Two is equal two =)');
    }

    private function _test_example_fails() {
        // This test will pass
        $this->unit->run(4, 2, 'Four is equal two??');
    }
}

/* End of file tests.php */
/* Location: ./application/controllers/tests.php */
