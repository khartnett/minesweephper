<?php
require_once 'Grid.php';
class Game {
    protected $gameOn = true;
    protected $grid;
    protected $climate;

    public function run() {
        $this->climate = new League\CLImate\CLImate;
        $this->newGame(9,9,10);
        while($this->gameOn) {
            $this->draw();
            $response = $this->getInput();
            $this->handleResponse($response);
        }
    }

    protected function draw() {
        // todo: move to view
        $this->climate->clear();
        if ($this->grid) {
            $this->climate->redTable($this->grid->getViewGrid());
        }
    }

    protected function getInput() {
        $input = $this->climate->input('(q)uit or Row then cell:');
        return $input->prompt();
    }

    protected function handleResponse($response) {
        if ($response === 'q' || $response === "quit") {
            $this->gameOn = false;
            return;
        }
        $flag = false;
        if ($this->getChar($response, 0) === '-') {
            //flag
            $response = substr($response, 1);
            $flag = true;
        }
        $cellPosition = $this->readCellPosition($response);

        if ($cellPosition) {
            $cell = $this->grid->getCell($cellPosition[0], $cellPosition[1]);
            if ($flag) {
                $cell->toggleFlagged();
            } else {
                $this->stepCell($cell);
            }
        } else {
            // don't understand input
        }
    }

    private function readCellPosition($data) {
        $cellPosition1 = strtoupper($this->getChar($data, 0));
        $cellPosition2 = strtoupper($this->getChar($data, 1));
        if ($cellPosition2 && ord($cellPosition2) > 64) {
            // if second character is a letter
            $row = intval($cellPosition1);
            $col = ord($cellPosition2) - 64;
        } else if ($cellPosition1 && ord($cellPosition1) > 64) {
            // if second character is a letter
            $row = intval($cellPosition2);
            $col = ord($cellPosition1) - 64;
        } else {
            return array();
        }
        return array($row, $col);
    }

    private function getChar($text, $char) {
        if (isset($text[$char])) {
            return $text[$char];
        }
        return false;
    }

    protected function newGame($sizeW, $sizeH, $mines) {
        $this->grid = new Grid($sizeW, $sizeH, $mines);
    }

    protected function stepCell($cell) {
        $value = $cell->reveal();
        if ($value === " ") {
            $neighbors = $this->grid->getCellNeighbors($cell->getRow(), $cell->getCol());
            foreach ($neighbors as $neighbor) {
                if (!$neighbor->isVisible()) {
                    $this->stepCell($neighbor);
                }
            }
        } else if ($cell->isMine()) {
            $this->lose();
        }
    }

    protected function lose() {
        $this->grid->revealMines();
    }
}