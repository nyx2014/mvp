<?php
    $page['title'] = 'elementary OS';
    $page['scripts'] = '<link rel="stylesheet" type="text/css" media="all" href="styles/countdown.css">';
    include __DIR__.'/_templates/sitewide.php';
    include $template['header'];
    include $template['alert'];
?>

<div class="grid">
    <div class="countdown__group">
        <div class="countdown__number">1</div>
        <div class="countdown__number">2</div>
    </div>
    <div class="countdown__group">
        <div class="countdown__number">3</div>
        <div class="countdown__number">4</div>
    </div>
    <div class="countdown__group">
        <div class="countdown__number">5</div>
        <div class="countdown__number">6</div>
    </div>
</div>

<?php
    include $template['footer'];
?>
