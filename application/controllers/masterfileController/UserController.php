<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usercontroller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('user_model');
        date_default_timezone_set('Asia/Manila');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function getUsers()
    {
        $users = $this->user_model->getUsers();
        return JSONResponse($users);
    }

    public function addUser()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $name = '';
        $duplicate = array();
        $userData = array();
        $m = array();

        if (!empty($data)) {

            $this->db->trans_start();
            $duplicate = $this->user_model->duplicate($data['username']);
            if (empty($duplicate)) {
                if (!empty($data['middlename'])) {
                    $name = $data['firstname'] . ' ' . $data['middlename'] . ' ' . $data['lastname'];
                } else {
                    $name = $data['firstname'] . ' ' . $data['lastname'];
                }
                $userData =
                    [
                        'name' => $name,
                        'position' => $data['position'],
                        'department' => $data['department'],
                        'subsidiary' => $data['subsidiary'],
                        'userType' => $data['usertype'],
                        'username' => $data['username'],
                        'password' => password_hash('Cwo_2021', PASSWORD_DEFAULT),
                        'status' => 'Active'
                    ];

                $this->db->insert('users', $userData);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Saving User', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed saving data.', 'info' => 'Error Saving'];
                } else {
                    $m = ['message' => 'User Successfully Saved.', 'info' => 'Success'];
                }
            } else {
                $m = ['message' => 'Username already exists.', 'info' => 'Duplicate'];
            }
        } else {

            $m = ['message' => 'Failed Saving User: No Data.', 'info' => 'No Data'];
        }

        JSONResponse($m);
    }

    public function updateUser()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $m = array();
        $userData = array();

        if (!empty($data)) {

            $this->db->trans_start();

            $userData =
                [
                    'userType' => $data['usertype']
                ];

            $this->user_model->update('users', $userData, 'user_id = ' . $data['ID']);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Updating User', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $m = ['message' => 'Error: Failed Updating user.', 'info' => 'Error Saving'];
            } else {
                $m = ['message' => 'User Successfully Updated.', 'info' => 'Updated'];
            }
        } else {

            $m = ['message' => 'Failed Updating data: No Data', 'info' => 'No Data'];
        }

        JSONResponse($m);
    }

    public function deactivate()
    {
        $ID = $this->input->post('ID');
        $m = array();

        if (!empty($ID)) {
            $this->db->trans_start();

            $this->db->where('user_id', $ID);
            $this->db->update('users', ['status' => 'Deactivated']);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Deactivating User', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $m = ['message' => 'Error: Failed Deactivating user.', 'info' => 'Error Deactivating'];
            } else {
                $m = ['message' => 'User Successfully Deactivated.', 'info' => 'Deactivated'];
            }
        } else {
            $m = ['message' => 'Failed deactivating user: No ID found.', 'info' => 'No ID'];
        }

        JSONResponse($m);
    }

    public function getoldpass()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $user = $this->user_model->getUserById($this->session->userdata('user_id'));
        if (!empty($user)) {
            if (password_verify($fetch_data['oldpass'], $user->password)) {
                die('same');
            }
        }
        die('diff');
    }

    public function changepassword()
    {
        $fetch_data = $this->input->post(NULL, TRUE);

        $this->db->trans_start();

        $data = ['password' => password_hash($fetch_data['newpass'], PASSWORD_DEFAULT)];

        $this->user_model->update('users', $data, 'user_id = ' . $this->session->userdata('user_id'));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Change Password', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $m = ['message' => 'Error: Failed to change password.', 'info' => 'Error'];
        } else {
            $m = ['message' => 'Password changed successfully!<br> You will be logged out.', 'info' => 'Success'];
        }

        JSONResponse($m);
    }

    public function resetpassword()
    {
        $fetch_data = $this->input->post(NULL, TRUE);

        $this->db->trans_start();

        $data = ['password' => password_hash('Cwo_2021', PASSWORD_DEFAULT)];

        $this->user_model->update('users', $data, 'user_id = ' . $fetch_data['userid']);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Reset Password', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $m = ['message' => 'Error: Failed to reset password.', 'info' => 'Error'];
        } else {
            $m = ['message' => 'Password was reset successfully!', 'info' => 'Success'];
        }

        JSONResponse($m);
    }

    public function searchEmployee()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $result = $this->user_model->searchemp($fetch_data['str']);
        JSONResponse($result);
    }

    public function addEmployee()
    {
        $fetch_data = $this->input->post(NULL, TRUE);
        $getExistence = $this->user_model->checkUserExistence($fetch_data['emp_id']);

        if ($getExistence) {
            $m = ['message' => 'Employee already exists!', 'info' => 'Error'];
        } else {

            $this->db->trans_start();

            $userData =
                [
                    'name' => strtoupper($fetch_data['name']),
                    'position' => $fetch_data['position'],
                    'department' => "",
                    'subsidiary' => "",
                    'userType' => "",
                    'province_id' => 0,
                    'username' => $fetch_data['emp_id'],
                    'emp_id' => $fetch_data['emp_id'],
                    'password' => password_hash('Cwo_2021', PASSWORD_DEFAULT),
                    'company_code' => $fetch_data['company_code'],
                    'bunit_code' => $fetch_data['bunit_code'],
                    'dept_code' => $fetch_data['dept_code'],
                    'added_by' => $this->session->userdata('user_id'),
                    'status' => 'Active'
                ];

            $this->db->insert('users', $userData);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Saving User', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $m = ['message' => 'Error: Failed saving data.', 'info' => 'Error'];
            } else {
                $m = ['message' => 'User successfully added.', 'info' => 'Success'];
            }
        }
        JSONResponse($m);
    }

    public function getOldUsername()
    {
        $user = $this->user_model->getUserById($this->session->userdata('user_id'));
        JSONResponse($user->username);
    }

    public function changeUsername()
    {
        $fetch_data = $this->input->post(NULL, TRUE);

        $this->db->trans_start();

        $getDupes = $this->user_model->duplicate($fetch_data['newuser']);

        if (!is_null($getDupes)) {
            $m = ['message' => 'Username already exists!', 'info' => 'Info'];

        } else {

            $user = $this->user_model->getUserById($this->session->userdata('user_id'));

            if ($user->username != $fetch_data['newuser']) {
                $data = ['username' => $fetch_data['newuser']];
                $this->user_model->update('users', $data, 'user_id = ' . $this->session->userdata('user_id'));
                $m = ['message' => 'Username changed successfully!<br> You will be logged out.', 'info' => 'Success'];

            } else {
                $m = ['message' => 'No changes detected!', 'info' => 'Info'];
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Change Username', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $m = ['message' => 'Error: Failed to change username.', 'info' => 'Error'];
        }

        JSONResponse($m);

    }


}
