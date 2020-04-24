<?php
/**
 * Created by PhpStorm.
 * User: RobertLee
 * Date: 2020/4/22
 * Time: 15:12:29.
 */
require dirname(__DIR__).'/vendor/autoload.php';

use App\Entity\BoardEntity;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$app = new Application('生命游戏', 'v0.9');

$app->register('start')
    ->addArgument('seed', InputArgument::OPTIONAL, '种子["glider","bang","big bang"]', 'big bang')
    ->addArgument('width', InputArgument::OPTIONAL, '棋盘宽度', '50')
    ->addArgument('height', InputArgument::OPTIONAL, '棋盘长度', '50')
    ->addArgument('speed', InputArgument::OPTIONAL, '思必得(us)', '500000')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        cls($output);
        $board = BoardEntity::getInstance((int) $input->getArgument('width'), (int) $input->getArgument('height'), $input->getArgument('seed'));
//     程序主循环
        while (true) {
            $output->write($board);
            $board->stepOne();
            usleep($input->getArgument('speed'));
            cls($output);
        }
    });

$app->run();

function cls(OutputInterface $output)
{
    $temp = '';
    for ($i = 1; $i <= 200; ++$i) {
        $temp .= "\n";
    }
    $output->write($temp);
}
