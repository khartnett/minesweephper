<?php
namespace Model;

use \Model\Cell;

class Grid {
    protected $gridArray;
    protected $width;
    protected $height;

    public function __construct($sizeW, $sizeH, $mines) {
        $this->width = $sizeW;
        $this->height = $sizeH;

        $this->buildGrid();
        $this->placeMines($mines);
        $this->setupNumbers();
    }

    public function getHeight() {
        return $this->height;
    }

    public function getWidth() {
        return $this->width;
    }

    private function buildGrid() {
        $this->gridArray = array();
        for ($row = 1; $row <= $this->height; $row++) {
            for ($col = 1; $col <= $this->width; $col++) {
                $this->gridArray[] = new Cell($row, $col);
            }
        }
    }

    private function placeMines($mines) {
        if ($mines > count($this->gridArray)) {
            throw new Exception('More mines than cells');
        }
        while ($mines) {
            $id = rand(0, count($this->gridArray) - 1);
            if (!$this->gridArray[$id]->isMine()) {
                $this->gridArray[$id]->setMine();
                $mines--;
            }
        }
    }

    private function setupNumbers() {
        foreach ($this->gridArray as $gridCell) {

            for ($row = 1; $row <= $this->height; $row++) {
                for ($col = 1; $col <= $this->width; $col++) {
                    $cell = $this->getCell($row, $col);
                    if (!$cell->isMine()) {
                        $neighbors = $this->getCellNeighbors($row, $col);
                        $mines = 0;
                        foreach ($neighbors as $neighbor) {
                            if ($neighbor->isMine()) {
                                $mines++;
                            }
                        }
                        if ($mines) {
                            $cell->setValue("$mines");
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns cell at row and col position base 1. False if no cell found.
     *
     * @param $row
     * @param $col
     * @return Cell | bool
     */
    public function getCell($row, $col) {
        if ($row > $this->height || $row < 1 || $col > $this->width || $col < 1) {
            return false;
        }
        $id = (($row - 1) * $this->width) + $col - 1;
        return $this->gridArray[$id];
    }

    public function getCellNeighbors($row, $col) {
        $neighbors = array();

        $topLeft = $this->getCell($row - 1 , $col - 1);
        if ($topLeft) { $neighbors[] = $topLeft; }
        $top = $this->getCell($row - 1 , $col);
        if ($top) { $neighbors[] = $top; }
        $topRight = $this->getCell($row - 1 , $col + 1);
        if ($topRight) { $neighbors[] = $topRight; }

        $left = $this->getCell($row, $col - 1);
        if ($left) { $neighbors[] = $left; }
        $right = $this->getCell($row, $col + 1);
        if ($right) { $neighbors[] = $right; }

        $bottomLeft = $this->getCell($row + 1 , $col - 1);
        if ($bottomLeft) { $neighbors[] = $bottomLeft; }
        $bottom = $this->getCell($row + 1 , $col);
        if ($bottom) { $neighbors[] = $bottom; }
        $bottomRight = $this->getCell($row + 1 , $col + 1);
        if ($bottomRight) { $neighbors[] = $bottomRight; }

        return $neighbors;
    }

    public function revealMines() {
        foreach ($this->gridArray as $cell) {
            if ($cell->isMine()) {
                $cell->reveal();
            }
        }
    }

}