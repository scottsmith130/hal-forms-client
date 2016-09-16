<?php

    $countForms = 0;
    $ch = curl_init('http://rwcbook08.herokuapp.com/task/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $body = json_decode(curl_exec($ch));

    $countLinks = count((array) $body->_links);
    $urls = [];
    $bodies = [];
    $rels = [];
    foreach ($body->_links as $rel => $link)
    {
        if (strpos($rel, 'http') === 0)
        {
            $ch = curl_init($rel);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/prs.hal-forms+json']);
            $response = json_decode(curl_exec($ch));

            if (!empty($response->_templates))
            {
                $countForms++;
                $urls[] = $link->href;
                $bodies[] = $response;
                $rels[] = $rel;
            }
        }
    }

    curl_close($ch);

?>
<html>
<body>
Number of links: <?php echo $countLinks ?><br/>
Number of forms found: <?php echo $countForms ?><br/>
<?php
foreach ($bodies as $i => $body)
{
    echo "<br/><br/>";
    echo '<a href="' . $rels[$i] . '">' . $rels[$i] . '</a>';
    echo "<br/><br/>";
    echo $body->_templates->default->title ?>
<form method="<?php echo $body->_templates->default->method ?>" action="<?php echo $_SERVER['PHP_SELF'] ?>">
    <input type="hidden" name="action" value="<?php echo $urls[$i] ?>"/>
    <?php
    foreach ($body->_templates->default->properties as $field)
    {
        echo $field->prompt;
        ?>
        <input type="text" name="<?php echo $field->name ?>" value="<?php echo $field->value ?>"/><br/>
        <input type="hidden" name="<?php echo $field->name . '-required'?>" value="<?php echo $field->required ?>"/>
        <?php
    }

    ?>
    <input type="submit"/>
</form>
    <?php } ?>
</body>
</html>