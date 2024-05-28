# 🦋 StarGatePolyfill
> 스타게이트를 확장해 추가로 API를 제공하는 Virion

# Notice
`StarGateAddon`, `XuidCore`와 함께 사용하면 안정적인 이용이 가능합니다

# API

업데이트 알림 송수신
```php
use kim\present\polyfill\stargate\StarGatePolyfill;
use kim\present\polyfill\stargate\UpdateNotifyModel;

//모델 클래스
class WarpUpdateNotifyModel implements UpdateNotifyModel{

    public string $warpName;

    public function getId() : string{
        return "warp.update";
    }
    public function handle(self $model) : void{
        var_dump($model->warpName);
    }
}

//업데이트 알림을 받기 위한 모델을 등록하기 위한 유틸 메소드
StarGatePolyfill::registerNotifyModel(UpdateNotifyModel $updateNotifiyModel) : void;

//업데이트 알림을 보내기 위한 유틸 메소드
//tarGateAddon이 존재하면 StarGateAddon을 통해 알림을 전송하고, 그렇지 않으면 바로 처리
StarGatePolyfill::sendNotify(UpdateNotifyModel $updateNotifiyModel) : void;
```

플레이어 객체를 구하기 위한 유틸 메소드, 만약 XuidCore가 존재하면 xuid 탐색을 지원
```php
use kim\present\polyfill\stargate\StarGatePolyfill;
use pocketmine\player\Player;

StarGatePolyfill::getPlayer(string|int $target) : ?Player;
```

다양한 메시지 전송
```php
use kim\present\polyfill\stargate\StarGatePolyfill;

//대상 플레이어에게 메시지를 전송하기 위한 유틸 메소드
StarGatePolyfill::sendMessage(string|int $target, string $message) : void;

//모든 플레이어에게 메시지를 전송하기 위한 유틸 메소드
StarGatePolyfill::broadcastMessage(string $message) : void;

//대상 플레이어에게 토스트를 전송하기 위한 유틸 메소드
StarGatePolyfill::sendToast(string|int $target, string $title, string $body) : void;

//모든 플레이어에게 토스트를 전송하기 위한 유틸 메소드
StarGatePolyfill::broadcastToast(string $title, string $body) : void;
```

서버가 Workflow 서버인지 확인, StarGateAddon이 없으면 무조건 Workflow 서버로 간주<br>
Workflow 서버는 한 서버에서만 실행되어야하는 기능이 실행될 대상 서버<br>
ex) 거래소 자동 반환 등
```php
use kim\present\polyfill\stargate\StarGatePolyfill;

StarGatePolyfill::isWorkflowServer() : bool;
```


