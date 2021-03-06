<?php
namespace Model;

use \Model\Grid;
use \View\Terminal;
use \League\CLImate\Climate;

class Game {
    protected $running = true;
    protected $gameOn = false;
    protected $grid;
    protected $gameView;

    public function run() {
        $this->gameView = new Terminal(new CLImate(), 4);
        $this->newGame(9,9,10);
        while($this->running) {
            $this->draw();
            $response = $this->getInput();
            $this->handleResponse($response);
        }
    }

    protected function draw() {
        // todo: move to view
        $this->gameView->clear();
        if ($this->grid) {
            $this->gameView->drawGrid($this->grid);
        }
        $this->gameView->drawMessages();
        $this->gameView->clearMessages();
    }

    protected function getInput() {
        if ($this->gameOn) {
            return $this->gameView->prompt('(n)ew game, (q)uit, (B4) to reveal, (-B4) to flag:');
        } else {
            return $this->gameView->prompt('(n)ew game, (q)uit:');
        }
    }

    protected function handleResponse($response) {
        if ($response === 'q' || $response === "quit") {
            $this->running = false;
            $this->gameView->clear();
            return;
        }
        if ($response === 'n') {
            $this->newGame(9,9,10);
            return;
        }
        if (!$this->gameOn) {
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
            $this->gameView->addMessage("I'm sorry, I don't understand your input");
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
        $this->gameOn = true;
        $this->grid = new Grid($sizeW, $sizeH, $mines);
    }

    protected function stepCell($cell) {
        if (!$cell) {
            $this->gameView->addMessage("Cell not found");
            return;
        }
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
        $this->gameOn = false;
        $this->grid->revealMines();
        $this->gameView->addMessage("You Lose!");
    }
}