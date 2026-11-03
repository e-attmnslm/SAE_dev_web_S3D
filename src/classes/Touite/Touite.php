<?php
declare(strict_types=1);
class Touite {
    public string $auteur, $date, $tag, $img;
    public int $score;
    public function __construct(string $auteur, String $date, public string $texte, ?string $tag, ?string $img) {
        $this->auteur = $auteur;
        $this->date = $date;
        $this->tag = $tag;
        $this->img = $img;
    }

}