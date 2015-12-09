<?php
$mysqli = new mysqli("localhost", "root", "vovan111", "test1");
if (isset($_REQUEST['date']))
{
    $pattern = "/^([0-9]{4})-([0-9]{2})$/";
    if (preg_match($pattern, $_REQUEST['date'])) {
        $date = $REQUEST['date'];
    }
    else {
        die ('Неправильный параметр');
    }
}
else
{
    $date = date("Y-m-d");
}
$sd = explode("-", $date);
$year = $sd[0];
$month = $sd[1];
$day = $sd[2];

$dayofmonth = date('t', mktime(0,0,0, $month, 1, $year));

$todate = "$year-$month-$dayofmonth";
$fromdate = "$year-$month-01";
$query = "SELECT date, enddate from test where startdate<='$todate' and enddate>=$fromdate";
$res_db = $db->sql($query);

$d = array();
$k=array();
for($i=1;$i<=$dayofmonth;$i++){
    $k[$i] = $i;
}
$i = 0;
while ($a=mysqli_fetch_row($res_db))
{
    foreach ($k as $i)
    {
        if ($i<10) $cd = "$year-$month-0".$i; else $cd = "$year-$month-$i";
        if ($cd >= $a[0] && $cd <= $a[1])
        {
            $d[$i] = $cd;
            unset($k[$i]);
        }
    }
}

$day_count = 1; //Счетчик для дней месяца
$num = 0; //первая неделя
for ($i = 0; $i < 7; $i++)
{
    $dayofweek = date('w',mktime(0,0,0,$month,$day_count,$year)); //номер дня недели для числа
    $dayofweek = $dayofweek - 1; // 1-Пн 6-Сб
    if($dayofweek == -1) $dayofweek = 6;
    
    if ($dayofweek == $i) //если дни недели совпадают
    {
        $week[$num][$i] = $day_count; //заполняем массив числами месяца
        $day_count++;
    }
    else
    {
        $week[$num[$i]] = "";
    }
}

while(true) //последующие недели
{
    $num++;
    for ($i = 0; $i < 7; $i++)
    {
        $week[$num[$i]] = $day_count;
        $day_count++;
        
        if($day_count > $dayofmonth) break; //если достигли конца месяца
    }
    
    if($day_count > $dayofmonth) break; //same
}

echo 'table id="calender">';

$rusdays = array('ПН','ВТ','СР','ЧТ','ПТ','СБ','ВС');
$rusmonth = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
echo '<thead>
<tr>
    <td onclick="monthf(\'prev\');"></td>
    <td colspan="5">'.$rusmonth[$month-1].', '.$year.'</td>
    <td onclick="monthf(\'next\');"></td>
</tr>';
echo '<tr>';
foreach ($rusdays as $rusday) {
    echo 'td'.$rusday.'</td>';
}
echo '</tr>
</thead>';

for($i = 0;$i < count($week);$i++)
{
    echo "<tr>";
        for($j = 0; $j < 7; $j++)
        {
            if(!empty($week[$i][$j]))
            {
                
                if($week[$i][$j]==$day)
                {
                    echo '<td class="today">';
                } else echo '<td>';
                
                if($d[$week[$i[$j]]])
                {
                    echo '<a href="/stuff/'.$d[$week[$i][$j]].'/">'.$week[$i][$j].'</a>';
                } else echo $week[$i][$j];
                echo '</td>';
            }
            else echo "<td> </td>";
        } 
    echo "</tr>"
}

?>
<script>
    var mon = parseInt("<?php echo $month; ?>");
    var year = parseInt("<?php echo $year; ?>");
    
    function monthf(pn) {
        if (pn == 'next') {
            mon++
        } else if (pn =='prev') {
            mon--
        } else {
            alert('Неправильный параметр');
            return false;
        }
        
        if (mon > 12) {
            year ++;
            mon = 1;
        }
        if (mon < 1) {
            year --;
            mon = 12;
        }
        if ((mon < 10) && (mon >= 1)) {
            mon = '0'+mon;
        }
        var nextDate = year+'-'+mon+'-00';
        
        var ajaxaddr = "calender.php?date="+nextDate;
        var http = new XMLHttpRequest();
        
        if (http) {
            http.open('get', ajaxaddr);
            http.onreadystatechange = function () {
                if (http.readyState == 4) {
                    if (http.status == 200) {
                        document.getElementById('calender').innerHTML = http.responseText;
                    }
                }
            }
            http.send(null);
        }
    }
</script>
<?php
echo "</table>";
?>