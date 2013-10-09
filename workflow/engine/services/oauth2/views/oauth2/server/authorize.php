
<p>
    <strong><?php echo $response['client_details']['CLIENT_NAME']?></strong> would like to access the following data:
</p>
<ul>
    <?php foreach($response['requestedScope'] as $scope) {?>
    <li><?php echo $response['supportedScope'][$scope] ?></li>
    <?php } ?>
</ul>
<p>It will use this data to:</p>
<ul>
    <li>integrate with ProcessMaker</li>
    <li>make your life better</li>
    <li>miscellaneous nefarious purposes</li>
</ul>
<ul class="authorize_options">
    <li>
        <form action="/api/1.0/workflow/authorize?<?php echo $response['query_string']?>" method="post">
            <input type="submit" class="button authorize" value="Yes, I Authorize This Request"/>
            <input type="hidden" name="authorize" value="1"/>
        </form>
    </li>
    <li class="cancel">
        <form id="cancel" action="/api/1.0/workflow/authorize?<?php echo $response['query_string']?>" method="post">
            <a href="#" onclick="document.getElementById('cancel').submit()">cancel</a>
            <input type="hidden" name="authorize" value="0"/>
        </form>
    </li>
</ul>
