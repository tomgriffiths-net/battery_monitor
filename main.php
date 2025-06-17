<?php
class battery_monitor{
    public static function init():void{
        $defaultSettings = [
            'shutdownTimer' => 600,
            'shutdownCommand' => 'shutdown /s /f /t 0',
            'batteryInterval' => 30
        ];

        foreach($defaultSettings as $name => $value){
            settings::set($name, $value, false);
        }
    }
    public static function isDischarging():bool{
        exec('WMIC Path Win32_Battery Get BatteryStatus',$output);
        return (intval($output[1]) === 1);
    }
    public static function command($line):void{
        if($line === "loop"){
            self::monitor();
        }
        else{
            if(self::isDischarging()){
                echo "Battery is discharging\n";
            }
            else{
                echo "Battery is plugged in\n";
            }
        }
    }
    private static function monitor():void{
        echo "Monitoring battery...\n";
        
        $batteryInterval = settings::read('batteryInterval');
        if(!is_int($batteryInterval)){$batteryInterval = 30;}
        $batteryInterval = min(max($batteryInterval, 5), 600);

        $shutdownTimer = settings::read('shutdownTimer');
        if(!is_int($shutdownTimer)){$shutdownTimer = 600;}
        $shutdownTimer = min(max($shutdownTimer, 5), 3600);

        $shutdownCommand = settings::read('shutdownCommand');
        if(!is_string($shutdownCommand) || empty($shutdownCommand)){
            $shutdownCommand = 'shutdown /s /f /t 0';
        }

        $lastDischargeTime = null;

        while(true){
            if(self::isDischarging()){
                if($lastDischargeTime === null){
                    $lastDischargeTime = time();
                }
            }
            else{
                $lastDischargeTime = null;
            }

            if($lastDischargeTime !== null){
                $timeDiff = time() - $lastDischargeTime;
                if($timeDiff > $shutdownTimer){
                    mklog(2,'Running shutdown command (discharging for ' . $timeDiff . ' seconds)');
                    sleep(1);
                    exec($shutdownCommand, $output, $returnCode);
                    if($returnCode !== 0){
                        mklog(2, 'Shutdown command failed: ' . implode(' ', $output));
                    }
                    break;
                }
            }
            
            sleep($batteryInterval);
        }
    }
}