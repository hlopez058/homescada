    <?php
    if (isset($_POST['action'])) {
      
        switch ($_POST['action']) 
        {
            case 'ON':
                ON();
                break;
            case 'OFF':
                OFF();
                break;
            
            default :
                $t = str_replace("DIMrgb(","",$_POST['action']);
                $tt = str_replace(")", "", $t);
                $arr= explode(',', $tt);
                $r=$arr[0];
                $g=$arr[1];
                $b=$arr[2];

                //DIM(intval($r),intval($g),intval($b));
                DIM($r,$g,$b);
        }
    }


    function DIM($r,$g,$b) {
        //DAVE FIX THIS FUNCTION
        $str = 'python /home/pi/PIGPIO/ledControlPythons/led.py '.$r.' '.$g.' '.$b;
        $command = 
        escapeshellcmd($str);
        //$command = escapeshellcmd('python /home/pi/PIGPIO/ledControlPythons/led.py 0 0 0');
        $output = shell_exec($command);
        echo $output;
        exit;
    }


    function ON() {
        echo "Running ON";
        $command = escapeshellcmd('python /home/pi/PIGPIO/ledControlPythons/led.py 255 255 255');
        $output = shell_exec($command);
        echo $output;
        exit;
    }

    function OFF() {
        echo "Running OFF";
        $command = escapeshellcmd('python /home/pi/PIGPIO/ledControlPythons/led.py 0 0 0');
        $output = shell_exec($command);
        echo $output;
        exit;
    }
    ?>