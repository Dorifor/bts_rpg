<?php

namespace App;

use App\Utilitaires\Titre;
use App\Utilitaires\Couleurs;

class Map
{
    const POSITION_VIDE = "-";
    const POSITION_HERO = Couleurs::GREEN . "H" . Couleurs::RESET;
    const POSITION_OBSTACLE = Couleurs::YELLOW . "O" . Couleurs::RESET;
    const POSITION_PERSONNAGE = "P";

    const SEPARATEUR = Couleurs::DARK_GRAY .  " | " . Couleurs::RESET;

    const mapPremade = [
        [self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE, self::POSITION_VIDE, self::POSITION_VIDE, self::POSITION_OBSTACLE],
        [self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE, self::POSITION_OBSTACLE]
    ];

    protected int $longueur;
    protected int $hauteur;

    protected Hero $hero;
    protected int $posXHero;
    protected int $posYHero;

    protected array $map;

    protected string $mapName;

    protected function __construct()
    {
    }

    public static function __constructFile($mapName, $heroName): Map
    {
        $map = new Map();
        $mapArray = $map->genererMapFromFichier($mapName);
        $map->map = $mapArray;
        $map->longueur = count($mapArray[0]);
        $map->hauteur = count($mapArray);
        $map->mapName = $mapName;
        $map->hero = new Hero($heroName);
        return $map;
    }

    public static function __constructDefault($longueur, $hauteur): Map
    {
        $map = new Map();
        $map->longueur = ($longueur > 0) ? $longueur : 10;
        $map->hauteur = ($hauteur > 0) ? $hauteur : 10;
        // $map->posXHero = rand(0, $map->longueur - 1);
        // $map->posYHero = rand(0, $map->hauteur - 1);
        $map->posYHero = 1;
        $map->posXHero = 1;
        $map->map = self::mapPremade;
        return $map;
    }

    public function visualiser(): string
    {
        $final = $this->genererInformations();
        $final .= $this->genererMap();
        $final .= $this->genererActions();
        return $final;
    }

    public function moveHero(int $posY, int $posX): void
    {
        $this->posYHero = $posY;
        $this->posXHero = $posX;
    }

    public function genererInformations(): string
    {
        $infos = Titre::createTitle('informations', $this->longueur * 4, '-', Couleurs::RED, Couleurs::YELLOW);
        $infos .= Couleurs::PURPLE . 'Dimensions' . Couleurs::RESET . ' : (' . Couleurs::GREEN . $this->hauteur . Couleurs::RESET . ', ' . Couleurs::GREEN . $this->longueur . Couleurs::RESET . ')' . PHP_EOL;
        $infos .= Couleurs::PURPLE . 'Vide' . Couleurs::RESET . ' : ' . Couleurs::YELLOW . self::POSITION_VIDE . PHP_EOL;
        $infos .= Couleurs::PURPLE . 'Hero' . Couleurs::RESET . ' : ' . Couleurs::YELLOW . $this->hero->getNom() . ' - ' . self::POSITION_HERO . Couleurs::RESET . ' (' . Couleurs::DARK_GRAY . $this->posYHero . Couleurs::RESET . ', ' . Couleurs::DARK_GRAY . $this->posXHero . Couleurs::RESET . ')' . PHP_EOL;
        $infos .= Couleurs::PURPLE . 'Obstacle' . Couleurs::RESET . ' : ' . Couleurs::YELLOW . self::POSITION_OBSTACLE . PHP_EOL;
        $infos .= Couleurs::PURPLE . 'Personnage' . Couleurs::RESET . ' : ' . Couleurs::YELLOW . self::POSITION_PERSONNAGE . PHP_EOL;
        return $infos;
    }

    public function genererActions(): string
    {
        $actions = Titre::createTitle('actions', $this->longueur * 4, '-', Couleurs::RED, Couleurs::YELLOW);
        $actions .= '- haut / bas / gauche / droite' . PHP_EOL;
        $actions .= '- stop / save' . PHP_EOL;
        return $actions;
    }

    public function genererMap(): string
    {
        // Create Title
        $map = Titre::createTitle('map', $this->longueur * 4, '-', Couleurs::RED, Couleurs::YELLOW);
        $map .= str_repeat(' ', 2);
        for ($i = 0; $i < $this->longueur; $i++) {
            $map .= Couleurs::BLUE . sprintf('  %02d', $i) . Couleurs::RESET;
        }
        $map .= PHP_EOL;

        // $mapMatrice = $this->genererMapArray($this->posYHero, $this->posXHero);
        $nextMap = $this->map;
        $nextMap[$this->posYHero][$this->posXHero] = self::POSITION_HERO;

        for ($y = 0; $y < $this->hauteur; $y++) {
            $map .= $this->genererLigneMatrice($y, $nextMap);
        }
        return $map;
    }

    public function genererLigneMatrice(int $numeroLigne, array $mapMatrice): string
    {
        $ligne = "";
        $ligne .= Couleurs::BLUE . sprintf('%02d', $numeroLigne) . Couleurs::RESET;
        for ($x = 0; $x < $this->longueur; $x++) {
            $ligne .= self::SEPARATEUR . $mapMatrice[$numeroLigne][$x];
        }
        $ligne .= self::SEPARATEUR . PHP_EOL;
        return $ligne;
    }

    public function genererMapArray(): array
    {
        $mat = array();
        for ($y = 0; $y < $this->hauteur; $y++) {
            $ligne = array();
            for ($x = 0; $x < $this->longueur; $x++) {
                array_push($ligne, self::POSITION_VIDE);
            }
            array_push($mat, $ligne);
        }
        $mat[$this->posYHero][$this->posXHero] = self::POSITION_HERO;
        return $mat;
    }

    protected function genererMapFromFichier(string $mapName): array
    {
        $mapArray = [];
        $filepath = 'src/CustomMaps/' . $mapName;
        $f = fopen($filepath, 'rb');
        $map = fread($f, filesize($filepath));
        $lines = explode(PHP_EOL, $map);
        foreach ($lines as $line) {
            $newLine = [];
            $tiles = str_split($line);
            foreach ($tiles as $tile) {
                $posY = array_search($line, array_values($lines));
                $posX = array_search($tile, array_values($tiles));
                switch ($tile) {
                    case '-':
                        array_push($newLine, self::POSITION_VIDE);
                        break;

                    case 'H':
                        $this->posYHero = $posY;
                        $this->posXHero = $posX;
                        array_push($newLine, self::POSITION_VIDE);
                        break;

                    case 'O':
                        array_push($newLine, self::POSITION_OBSTACLE);
                        break;

                    default:
                        array_push($newLine, self::POSITION_VIDE);
                        break;
                }
            }
            array_push($mapArray, $newLine);
        }
        return $mapArray;
    }

    public function saveMap(string $mapName): void
    {
        // to save map on different file later
        $mapName = $this->mapName;
        $map = $this->map;
        $save = '';
        $map[$this->posYHero][$this->posXHero] = self::POSITION_HERO;
        foreach ($map as $line) {
            foreach ($line as $tile) {
                switch ($tile) {
                    case self::POSITION_VIDE:
                        $save .= '-';
                        break;

                    case self::POSITION_OBSTACLE:
                        $save .= 'O';
                        break;

                    case self::POSITION_HERO:
                        $save .= 'H';
                        break;

                    default:
                        $save .= '-';
                        break;
                }
            }
            $save .= PHP_EOL;
        }
        $save = trim($save);
        $path = 'src/CustomMaps/' . $mapName;
        $saveFile = fopen($path, 'wb');
        fwrite($saveFile, $save);
    }

    public function isObstacle(int $posY, int $posX): bool
    {
        return $this->map[$posY][$posX] == self::POSITION_OBSTACLE;
    }

    public function getPosXHero()
    {
        return $this->posXHero;
    }

    public function getPosYHero()
    {
        return $this->posYHero;
    }

    public function getLongueur()
    {
        return $this->longueur;
    }

    public function getHauteur()
    {
        return $this->hauteur;
    }

    // public function genererMap(): string
    // {
    //     $map = str_repeat(' ', 2);
    //     for ($i = 0; $i < $this->longueur; $i++) {
    //         $map .= Couleurs::BLUE . sprintf('  %02d', $i) . Couleurs::RESET;
    //     }
    //     $map .= PHP_EOL;

    //     for ($y = 0; $y < $this->hauteur; $y++) {
    //         $map .= $this->genererLigne($y);
    //     }
    //     return $map;
    // }

    // public function genererLigne(int $numeroLigne): string
    // {
    //     $ligne = "";
    //     $ligne .= Couleurs::BLUE . sprintf('%02d', $numeroLigne) . Couleurs::RESET;
    //     for ($x = 0; $x < $this->longueur; $x++) {
    //         $ligne .= self::SEPARATEUR . self::POSITION_VIDE;
    //     }
    //     $ligne .= self::SEPARATEUR . PHP_EOL;
    //     return $ligne;
    // }
}
