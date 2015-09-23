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
        //$response='[{"kind":"story_update_activity","guid":"1420598_110","project_version":110,"message":"Valdur Kana accepted this feature","highlight":"accepted","changes":[{"kind":"story","change_type":"update","id":102611636,"original_values":{"current_state":"delivered","accepted_at":null,"updated_at":"2015-09-15T21:42:16Z"},"new_values":{"current_state":"accepted","accepted_at":"2015-09-16T18:48:20Z","updated_at":"2015-09-16T18:48:20Z"},"name":"Profiili info","story_type":"feature"}],"primary_resources":[{"kind":"story","id":102611636,"name":"Profiili info","story_type":"feature","url":"https://www.pivotaltracker.com/story/show/102611636"}],"project":{"kind":"project","id":1420598,"name":"valdurist"},"performed_by":{"kind":"person","id":1781518,"name":"Valdur Kana","initials":"VK"},"occurred_at":"2015-09-16T18:48:20Z"},{"kind":"comment_create_activity","guid":"1420598_109","project_version":109,"message":"Valdur Kana added comment: \"Commit by Andbyu\nhttps://github.com/valdur55/valdurist/commit/7e73edd86233811b01d39c489a3d9e4c3062c87d\n\nLisatud kirjeldused piltide alla.\nLisatud lemmikraamatute nimekiri.\nLisatud typeracer\'i profiil.\nLisatud koolide nimekirja ka haridus.\n[Delivers #102611636]\"","highlight":"added comment:","changes":[{"kind":"comment","change_type":"create","id":112078434,"new_values":{"id":112078434,"story_id":102611636,"text":"Commit by Andbyu\nhttps://github.com/valdur55/valdurist/commit/7e73edd86233811b01d39c489a3d9e4c3062c87d\n\nLisatud kirjeldused piltide alla.\nLisatud lemmikraamatute nimekiri.\nLisatud typeracer\'i profiil.\nLisatud koolide nimekirja ka haridus.\n[Delivers #102611636]","person_id":1781518,"created_at":"2015-09-15T21:42:16Z","updated_at":"2015-09-15T21:42:16Z","file_attachment_ids":[],"google_attachment_ids":[],"commit_identifier":"7e73edd86233811b01d39c489a3d9e4c3062c87d","commit_type":"github","file_attachments":[],"google_attachments":[]}}],"primary_resources":[{"kind":"story","id":102611636,"name":"Profiili info","story_type":"feature","url":"https://www.pivotaltracker.com/story/show/102611636"}],"project":{"kind":"project","id":1420598,"name":"valdurist"},"performed_by":{"kind":"person","id":1781518,"name":"Valdur Kana","initials":"VK"},"occurred_at":"2015-09-15T21:42:16Z"}]';
        $act = json_decode($response);

        return $act;
        //echo "Viimase commiti aeg: ".($act[1]->occurred_at) . "<br>";
        //echo "Viimane commit: " . ($act[1]->changes[0]->new_values->text);
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
    <title>Staatiline ja sinisilmne lahendus</title>
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