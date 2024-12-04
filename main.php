<?php
//Your Variables go here: $GLOBALS['battery_monitor']['YourVariableName'] = YourVariableValue
class battery_monitor{
    public static function isDischarging():bool{
        exec('WMIC Path Win32_Battery Get BatteryStatus',$output);
        if(intval($output[1]) === 1){
            return true;
        }
        else{
            return false;
        }
    }
    public static function command($line):void{
        echo "Monitoring battery...\n";
        start:
        if(self::isDischarging()){
            mklog('warning','Battery is in discharging state, waiting 30 seconds');
            sleep(30);
            if(self::isDischarging()){
                mklog('warning','Battery has in discharging state for 30 seconds or more, shutting down computer');

                //Truenas check and power off
                if(class_exists('network') && class_exists('cmd') && class_exists('character_sender') && class_exists('txtrw')){
                    if(network::ping('truenas.local','80')){
                        cmd::newWindow('title TRUENAS_SSH && ssh root@truenas shutdown -p now');
                        sleep(2);
                        character_sender::sendString(base64_decode(base64_decode(txtrw::readtxt('p'))),"TRUENAS_SSH",true);
                    }
                }

                //Local machine shutdown
                exec('shutdown /s /f /t 0');
            }
        }
        if($line === "loop"){
            sleep(60);
            goto start;
        }
    }//Run when base command is class name, $line is anything after base command (string). e.g. > [base command] [$line]
    //public static function init():void{}//Run at startup
}