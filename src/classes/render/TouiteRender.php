<?php
declare(strict_types=1);

class TouiteRender{

    public function __construct(private Touite $touite) {
        $this->touite = $touite;
    }

    public function render(int $selector):String{
        switch ($selector){
            case 1:
                return $this->renderCompact();
                break;
            // case 2:
            //     return $this->renderLong();
            //     break;
            default:
                return "1 = compact, 2 = Long";
        }
    }

    public function renderCompact() : string {
        $v = $this->touite;
        return " $v->auteur <br>
                $v->texte <br>
                $v->date <br>
                $v->img";
    }
}