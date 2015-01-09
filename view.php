<?php


require_once 'vendor/autoload.php';

require_once 'model/Grid.php';

$grid = new Grid(9,9,10);

//$grid = new Grid(3,3,5);

//var_dump($grid->getViewGrid());
$climate = new League\CLImate\CLImate;

$gameOn = true;
while($gameOn) {
    $climate->clear();


    $climate->redTable($grid->getViewGrid());
    $input = $climate->input('(q)uit or Row then cell:');
    $response = $input->prompt();
    if ($response === 'q' || $response === "quit") {
        $gameOn = false;
    } else {
        $cellPosition = explode(',', $response);
        stepCell($grid, $grid->getCell(intval($cellPosition[0]),intval($cellPosition[1])));
        //$grid->getCell(intval($cellPosition[0]),intval($cellPosition[1]))->reveal();
    }
}

function stepCell($grid, $cell) {
    $value = $cell->reveal();
    if ($value === " ") {
        $neighbors = $grid->getCellNeighbors($cell->getRow(), $cell->getCol());
        foreach ($neighbors as $neighbor) {
            if (!$neighbor->isVisible()) {
                stepCell($grid, $neighbor);
            }
        }
    }
}