<?php
declare(strict_types=1);
class Touite {
    private string $auteur, $date, $tag, $img;
    public function __construct(string $auteur, String $date, ?string $tag, ?string $img) {
        $this->auteur = $auteur;
        $this->date = $date;
        $this->tag = $tag;
        $this->img = $img;
    }
}