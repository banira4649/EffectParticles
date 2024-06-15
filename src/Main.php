<?php

declare(strict_types=1);

namespace banira4649\EffectParticles;

use pocketmine\color\Color;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\Position;

class Main extends PluginBase{

    private string $molangVariablesJson;

    public function onEnable(): void{
        $this->molangVariablesJson = mb_convert_encoding(
            file_get_contents(__DIR__ . "\\molangVariables.json"),
            'UTF8',
            'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'
        );
        var_dump(json_decode($this->molangVariablesJson));
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(
            function(){
                foreach(Server::getInstance()->getOnlinePlayers() as $player){
                    foreach($player->getEffects()->all() as $effect){
                        $this->sendParticle($player->getPosition(), $effect->getColor());
                    }
                }
            }
        ), 10);
    }

    private function sendParticle(Position $position, Color $color): void{
        $packet = new SpawnParticleEffectPacket();
        $packet->position = $position->add(0, mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax(), 0);
        $packet->particleName = "minecraft:mobspell_emitter";
        $json = json_decode($this->molangVariablesJson);
        $json[0]->value->value[0]->value->value = $color->getR() / 255;
        $json[0]->value->value[1]->value->value = $color->getG() / 255;
        $json[0]->value->value[2]->value->value = $color->getB() / 255;
        $packet->molangVariablesJson = json_encode($json);
        NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [$packet]);
    }
}
