<?php
namespace View;

class Terminal
{
    protected $climate;
    protected $numMessageLines;
    protected $messages;
    protected $cellColors = array(
        '1' => 'light_blue',
        '2' => 'green',
        '3' => 'red',
        '4' => 'blue',
        '5' => 'dark_gray',
        '6' => 'cyan',
        '7' => 'black',
        '8' => 'light_gray',
        'F' => 'magenta',
        '*' => 'blink'
    );
    
    public function __construct($climate, $numMessageLines) {
        $this->climate = $climate;
        $this->numMessageLines = $numMessageLines;
        $this->clearMessages();
    }

    public function clear() {
        $this->climate->clear();
    }

    public function drawGrid($grid) {
        $this->climate->redTable($this->getViewGrid($grid));
    }

    public function getViewGrid($grid) {
        $viewGrid = array();
        for ($row = 1; $row <= $grid->getHeight(); $row++) {
            $newRow = array( ' ' => "$row");
            for ($col = 1; $col <= $grid->getWidth(); $col++) {
                $cell = $grid->getCell($row, $col);
                $newRow[chr(64 + $col)] = $this->decorateCellValue($cell->getPrintValue());
            }
            $viewGrid[] = $newRow;
        }
        return $viewGrid;
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

    protected function decorateCellValue($value) {
        if (isset($this->cellColors[$value])) {
            $style = $this->cellColors[$value];
            return "<$style>$value</$style>";
        }
        return $value;
    }
}