<?php
class User {
    private $name;
    private $email;
    private $password;
    private $phone;
    private $program;
    private $schedule;

    public function __construct($name, $email, $password, $phone, $program, $schedule) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->program = $program;
        $this->schedule = $schedule;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getProgram() {
        return $this->program;
    }

    public function getSchedule() {
        return $this->schedule;
    }

    public function isValidEmail() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }
}
?>
