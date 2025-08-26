<?php
class PackEtape {
    private int $id_packetape;
    private int $id_pack;
    private int $id_point;
    private int $ordre;

    public function __construct(int $id_pack, int $id_point, int $ordre) {
        $this->id_pack = $id_pack;
        $this->id_point = $id_point;
        $this->ordre = $ordre;
    }

    public function getIdPackEtape(): int { return $this->id_packetape; }
    public function setIdPackEtape(int $id): void { $this->id_packetape = $id; }

    public function getIdPack(): int { return $this->id_pack; }
    public function setIdPack(int $id): void { $this->id_pack = $id; }

    public function getIdPoint(): int { return $this->id_point; }
    public function setIdPoint(int $id): void { $this->id_point = $id; }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $o): void { $this->ordre = $o; }
}
