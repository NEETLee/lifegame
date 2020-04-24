<?php
/**
 * Created by PhpStorm.
 * User: RobertLee
 * Date: 2020/4/22
 * Time: 17:31:49.
 */

namespace App\Entity;

class BoardEntity
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var array
     */
    private $chesses;

    /**
     * @var int
     */
    private $step = 0;

    /**
     * @var float
     */
    private $speed;
    /**
     * @var \App\Entity\BoardEntity
     */
    private static $instances;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    public static function getInstance(int $width, int $height, string $seed = ''): BoardEntity
    {
        if (!isset(self::$instances)) {
            self::$instances = new static();
        }
        self::$instances->width = $width;
        self::$instances->height = $height;
        self::$instances->createWorld($seed);

        return self::$instances;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    private function pushChesses(ChessEntity $chess): void
    {
        $this->chesses[] = $chess;
    }

    private function pushLineBreak()
    {
        $this->chesses[] = "\n";
    }

    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * 把棋盘生成为字符串输出.
     *
     * @return string
     */
    public function __toString()
    {
        $temp = '';
        foreach ($this->yielded($this->chesses) as $chess) {
            $temp .= $chess;
        }

        return $temp;
    }

    /**
     * 通过算法生成一系列的坐标当作初始棋盘.
     */
    private function createWorld(string $seed = '')
    {
        $centreX = (int) $this->width / 2;
        $centreY = (int) $this->height / 2;
        if ('glider' == $seed) {
            $formation = [
                [$centreX, $centreY],
                [$centreX + 1, $centreY + 1],
                [$centreX - 1, $centreY + 2],
                [$centreX, $centreY + 2],
                [$centreX + 1, $centreY + 2],
            ];
        } elseif ('bang' == $seed) {
            $formation = [
                [$centreX, $centreY],
                [$centreX - 1, $centreY + 1],
                [$centreX, $centreY + 1],
                [$centreX + 1, $centreY + 1],
                [$centreX - 1, $centreY + 2],
                [$centreX + 1, $centreY + 2],
                [$centreX, $centreY + 3],
            ];
        } elseif ('big bang' == $seed) {
            $formation = [
                [$centreX, $centreY],
                [$centreX + 2, $centreY],
                [$centreX + 4, $centreY],
                [$centreX, $centreY + 1],
                [$centreX + 4, $centreY + 1],
                [$centreX, $centreY + 2],
                [$centreX + 4, $centreY + 2],
                [$centreX, $centreY + 3],
                [$centreX + 4, $centreY + 3],
                [$centreX, $centreY + 4],
                [$centreX + 2, $centreY + 4],
                [$centreX + 4, $centreY + 4],
            ];
        } else {
            $temp = md5(empty($seed) ? time() : $seed);
//        TODO::设计一种算法将md5字符串转换为个数随机的位于当前棋盘内的坐标
            $formation = [];
        }
        $this->initWorld($formation);
    }

    private function initWorld(array $formation)
    {
        for ($y = 1; $y <= $this->height; ++$y) {
            for ($x = 1; $x <= $this->width; ++$x) {
                if (isset($formation[0]) && $formation[0][0] === $x && $formation[0][1] === $y) {
                    $alive = true;
                    array_shift($formation);
                } else {
                    $alive = false;
                }
                $this->pushChesses(new ChessEntity([$x, $y], $alive));
            }
            $this->pushLineBreak();
        }
    }

    public function stepOne()
    {
        /** @var \App\Entity\ChessEntity $chess */
        foreach ($this->yielded($this->chesses) as $chess) {
            if ($chess instanceof ChessEntity) {
                $chess->setNeighbor($this->checkAround($chess));
                if (3 == $chess->getNeighbor()) {
                    $chess->setNext(true);
                } elseif (2 == $chess->getNeighbor()) {
                    $chess->setNext($chess->isAlive());
                } else {
                    $chess->setNext(false);
                }
            } else {
                continue;
            }
        }
        foreach ($this->yielded($this->chesses) as $chess) {
            if ($chess instanceof ChessEntity) {
                $chess->toNext();
            }
        }
    }

    private function checkAround(ChessEntity $chess)
    {
        $count = 0;
        [$x, $y] = $chess->getLocation();
        $neighbor = [
            [$x - 1, $y - 1],
            [$x, $y - 1],
            [$x + 1, $y - 1],
            [$x - 1, $y],
            [$x + 1, $y],
            [$x - 1, $y + 1],
            [$x, $y + 1],
            [$x + 1, $y + 1],
        ];
        foreach ($neighbor as $value) {
            if ($value[0] <= 0 || $value[1] <= 0 || $value[0] > $this->width || $value[1] > $this->height) {
                continue;
            } else {
                if ($this->chesses[$this->locationConvert($value, $this->width)]->isAlive()) {
                    ++$count;
                }
            }
        }

        return $count;
    }

    /**
     * 将坐标转换为索引.
     *
     * @return float|int|mixed
     */
    private function locationConvert(array $xy, int $w)
    {
        return $xy[0] + $xy[1] + $w * $xy[1] - $w - 2;
    }

    private function yielded(array $data)
    {
        foreach ($data as $datum) {
            yield $datum;
        }
    }
}
