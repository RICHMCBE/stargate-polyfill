<?php

/**
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author       PresentKim (debe3721@gmail.com)
 * @link         https://github.com/PresentKim
 * @license      https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\polyfill\stargate;

use alvin0319\StarGateAddon\StarGateAddon;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use RoMo\XuidCore\XuidCore;

use function class_exists;
use function is_numeric;

/** This trait override most methods in the {@link PluginBase} abstract class. */
final class StarGatePolyfill{

    private static bool $hasStarGate;

    public static function hasStarGate() : bool{
        return self::$hasStarGate ??= class_exists(StarGateAddon::class);
    }

    /**
     * 업데이트 알림을 받기 위한 모델을 등록하기 위한 유틸 메소드
     *
     * @param UpdateNotifyModel $model
     */
    public static function registerNotifyModel(UpdateNotifyModel $model) : void{
        if(!self::hasStarGate()){
            return;
        }

        StarGateAddon::getInstance()->registerUpdateNotify(
            $model->getId(),
            $model::class,
            $model->handle(...)
        );
    }

    /**
     * 업데이트 알림을 보내기 위한 유틸 메소드
     * StarGateAddon이 존재하면 StarGateAddon을 통해 알림을 전송하고, 그렇지 않으면 바로 처리
     *
     * @param UpdateNotifyModel $model
     */
    public static function sendNotify(UpdateNotifyModel $model) : void{
        if(self::hasStarGate()){
            StarGateAddon::notifyUpdate($model->getId(), $model);
        }else{
            $model->handle($model);
        }
    }

    /**
     * 플레이어 객체를 구하기 위한 유틸 메소드, 만약 XuidCore가 존재하면 xuid 탐색을 지원
     *
     * @param string|int $target 플레이어 이름 혹은 XUID
     *
     * @return Player|null
     */
    public static function getPlayer(string|int $target) : ?Player{
        $player = Server::getInstance()->getPlayerExact((string) $target);
        if($player === null && is_numeric($target) && class_exists(XuidCore::class)){
            $player = XuidCore::getInstance()->getPlayer((int) $target);
        }

        return $player;
    }

    /**
     * 대상 플레이어에게 메시지를 전송하기 위한 유틸 메소드
     *
     * @param string|int $target  플레이어 이름 혹은 XUID
     * @param string     $message 전송할 메시지
     */
    public static function sendMessage(string|int $target, string $message) : void{
        $player = self::getPlayer($target);
        if($player !== null){
            $player->sendMessage($message);
            return;
        }

        if(self::hasStarGate()){
            StarGateAddon::sendMessage((string) $target, $message);
        }
    }

    /**
     * 모든 플레이어에게 메시지를 전송하기 위한 유틸 메소드
     *
     * @param string $message 전송할 메시지
     */
    public static function broadcastMessage(string $message) : void{
        if(self::hasStarGate()){
            StarGateAddon::broadcastMessage($message);
            return;
        }

        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $player->sendMessage($message);
        }
    }

    /**
     * 대상 플레이어에게 메시지를 전송하기 위한 유틸 메소드
     *
     * @param string|int $target 플레이어 이름 혹은 XUID
     * @param string     $title  전송할 타이틀
     * @param string     $body   전송할 본문
     */
    public static function sendToast(string|int $target, string $title, string $body) : void{
        $player = self::getPlayer($target);
        if($player !== null){
            $player->sendTitle($title, $body);
            return;
        }

        if(self::hasStarGate()){
            StarGateAddon::sendToast((string) $target, $title, $body);
        }
    }

    /**
     * 모든 플레이어에게 메시지를 전송하기 위한 유틸 메소드
     *
     * @param string $title 전송할 타이틀
     * @param string $body  전송할 본문
     */
    public static function broadcastToast(string $title, string $body) : void{
        if(self::hasStarGate()){
            StarGateAddon::broadcastToast($title, $body);
            return;
        }

        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $player->sendTitle($title, $body);
        }
    }

    /**
     * 서버가 Workflow 서버인지 확인, StarGateAddon이 없으면 무조건 Workflow 서버로 간주
     * Workflow 서버는 한 서버에서만 실행되어야하는 기능이 실행될 대상 서버
     * ex) 거래소 자동 반환 등
     *
     * @return bool Workflow 서버인지 여부
     */
    public static function isWorkflowServer() : bool{
        return !self::hasStarGate() || StarGateAddon::getServerName() === "workflow";
    }
}
