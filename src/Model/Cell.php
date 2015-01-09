<?php
namespace Model;

class Cell {
    protected $row;
    protected $col;
    protected $visible = false;
    protected $flagged = false;
    protected $value = " ";

    const MINE = "<blink>*</blink>";
    const FLAG = "F";

    public function __construct($row, $col) {
        $this->row = $row;
        $this->col = $col;
    }

    public function getPrintValue() {
        if ($this->isVisible()) {
            return $this->getValue();
        }
        if ($this->isFlagged()) {
            return self::FLAG;
        }
        return '-';
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function isVisible() {
        return $this->visible;
    }

    public function reveal() {
        $this->visible = true;
        $this->flagged = false;
        return $this->getValue();
    }

    public function isMine() {
        return ($this->value === self::MINE);
    }

    public function setMine() {
        $this->value = self::MINE;
    }

    public function isFlagged() {
        return $this->flagged;
    }

    public function toggleFlagged() {
        if (!$this->isVisible()) {
            $this->flagged = !$this->flagged;
        }
    }

    public function getRow() {
        return $this->row;
    }

    public function getCol() {
        return $this->col;
    }
}