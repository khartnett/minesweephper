<?php
namespace View;

class Terminal
{
    protected $climate;
    protected $numMessageLines;
    protected $messages;

    public function __construct($climate, $numMessageLines) {
        $this->climate = $climate;
        $this->numMessageLines = $numMessageLines;
        $this->clearMessages();
    }

    public function clear() {
        $this->climate->clear();
    }

    public function drawGrid($grid) {
        $this->climate->redTable($grid);
    }

    public function drawMessages() {
        foreach(range(1, $this->numMessageLines) as $lineNum) {
            $message = (isset($this->messages[$lineNum - 1]) ? $this->messages[$lineNum - 1] : "");
            $this->climate->out($message);
        }
    }

    public function clearMessages() {
        $this->messages = array();
    }

    public function addMessage($message) {
        $this->messages[] = $message;
    }

    public function prompt($message) {
        $input = $this->climate->input($message);
        return $input->prompt();
    }
}