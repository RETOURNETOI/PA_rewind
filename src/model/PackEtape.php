<?php

class PackEtape
{
    private int $id_pack;
    private int $id_point;
    private ?int $id_hebergement;
    private int $ordre;

    public function __construct(int $id_pack = 0, int $id_point = 0, ?int $id_hebergement = null, int $ordre = 0)
    {
        $this->id_pack = $id_pack;
        $this->id_point = $id_point;
        $this->id_hebergement = $id_hebergement;
        $this->ordre = $ordre;
    }

    public function getIdPack(): int { return $this->id_pack; }
    public function setIdPack(int $id): void { $this->id_pack = $id; }

    public function getIdPoint(): int { return $this->id_point; }
    public function setIdPoint(int $id): void { $this->id_point = $id; }

    public function getIdHebergement(): ?int { return $this->id_hebergement; }
    public function setIdHebergement(?int $id): void { $this->id_hebergement = $id; }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $ordre): void { $this->ordre = $ordre; }
}
