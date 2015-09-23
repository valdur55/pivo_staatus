<?php
$projects = Array(1420598, 1425026, 1431288);
$project_id = 1420598;
function get_data($p_id, $endpoint)
{

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.pivotaltracker.com/services/v5/projects/$p_id/$endpoint",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
        //echo "cURL Error #:" . $err;
    } else {
        $act = json_decode($response);

        return $act;
    }
}

function get_row($pid)
{
    $row = Array();
    $data = get_data($pid, "");
    array_push($row, $data->name);
    $data = get_data($pid, "activity?limit=1&offset=0");
    array_push($row, $data[0]->message);
    array_push($row, $data[0]->occurred_at);
    $data = get_data($pid, "");
    //echo "Viimase commiti aeg: ".($act[1]->occurred_at) . "<br>";
    //echo "Viimane commit: " . ($act[1]->changes[0]->new_values->text);
    array_push($row, "Commit sonum");
    array_push($row, "Commit aeg");
    $data = get_data($pid, "stories?with_state=delivered");
    array_push($row, count($data));
    return $row;

}

$pealkiri = Array("Projekti nimi", "Viimase  kande pealkir", "Aeg", "Commiti message", "Aeg", "Acceptimata storyd");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pivotal_tracker tabelina</title>
</head>
<body>

<table border=1>

    <thead>

    <?php foreach ($pealkiri as &$p): ?>
        <th><?= $p ?></th>
    <? endforeach ?>

    </thead>

    <?php foreach ($projects as $pid):
        $rida = get_row($pid); ?>

        <tr>
            <? foreach ($rida as &$tulp): ?>

                <td><? $tulp ?></td>

            <? endforeach ?>
        </tr>

    <? endforeach ?>
</table>


</body>
</html>
