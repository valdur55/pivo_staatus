<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staatiline ja sinisilmne lahendus</title>
</head>
<body>
    
<?php

$curl = curl_init();
$project_id = 1420598;
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.pivotaltracker.com/services/v5/projects/$project_id/activity?limit=2&offset=0",
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
  echo "cURL Error #:" . $err;
} else {
    $response='[{"kind":"story_update_activity","guid":"1420598_110","project_version":110,"message":"Valdur Kana accepted this feature","highlight":"accepted","changes":[{"kind":"story","change_type":"update","id":102611636,"original_values":{"current_state":"delivered","accepted_at":null,"updated_at":"2015-09-15T21:42:16Z"},"new_values":{"current_state":"accepted","accepted_at":"2015-09-16T18:48:20Z","updated_at":"2015-09-16T18:48:20Z"},"name":"Profiili info","story_type":"feature"}],"primary_resources":[{"kind":"story","id":102611636,"name":"Profiili info","story_type":"feature","url":"https://www.pivotaltracker.com/story/show/102611636"}],"project":{"kind":"project","id":1420598,"name":"valdurist"},"performed_by":{"kind":"person","id":1781518,"name":"Valdur Kana","initials":"VK"},"occurred_at":"2015-09-16T18:48:20Z"},{"kind":"comment_create_activity","guid":"1420598_109","project_version":109,"message":"Valdur Kana added comment: \"Commit by Andbyu\nhttps://github.com/valdur55/valdurist/commit/7e73edd86233811b01d39c489a3d9e4c3062c87d\n\nLisatud kirjeldused piltide alla.\nLisatud lemmikraamatute nimekiri.\nLisatud typeracer\'i profiil.\nLisatud koolide nimekirja ka haridus.\n[Delivers #102611636]\"","highlight":"added comment:","changes":[{"kind":"comment","change_type":"create","id":112078434,"new_values":{"id":112078434,"story_id":102611636,"text":"Commit by Andbyu\nhttps://github.com/valdur55/valdurist/commit/7e73edd86233811b01d39c489a3d9e4c3062c87d\n\nLisatud kirjeldused piltide alla.\nLisatud lemmikraamatute nimekiri.\nLisatud typeracer\'i profiil.\nLisatud koolide nimekirja ka haridus.\n[Delivers #102611636]","person_id":1781518,"created_at":"2015-09-15T21:42:16Z","updated_at":"2015-09-15T21:42:16Z","file_attachment_ids":[],"google_attachment_ids":[],"commit_identifier":"7e73edd86233811b01d39c489a3d9e4c3062c87d","commit_type":"github","file_attachments":[],"google_attachments":[]}}],"primary_resources":[{"kind":"story","id":102611636,"name":"Profiili info","story_type":"feature","url":"https://www.pivotaltracker.com/story/show/102611636"}],"project":{"kind":"project","id":1420598,"name":"valdurist"},"performed_by":{"kind":"person","id":1781518,"name":"Valdur Kana","initials":"VK"},"occurred_at":"2015-09-15T21:42:16Z"}]';
    $act = json_decode($response);
    echo "Projekti ID: " . $project_id ." <br>";

    echo "Viimane uuendus: ".($act[0]->occurred_at) . "<br>";
    echo "Viimase commiti aeg: ".($act[1]->occurred_at) . "<br>";
    echo "Viimane commit: " . ($act[1]->changes[0]->new_values->text);
}
?>
</body>
</html>
