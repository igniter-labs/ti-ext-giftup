<div class="alert text-left text-reset">
    @if ($formModel->isConnected())
        <h4><i class="fa fa-circle text-success"></i>&nbsp;&nbsp;Connected to Gift Up!</h4>
    @else
        <h4><i class="fa fa-circle text-muted"></i>&nbsp;&nbsp;Connect to Gift Up!</h4>
    @endif
    <p>
        In order to sell gift cards on your TastyIgniter website, you need a free Gift Up!
        account connected to your TastyIgniter website. Follow the steps below ...
    </p>
    <ol class="pl-3">
        <li>
            <a href="https://giftup.app/account/register" target="_blank">Create a new</a>&nbsp;
            or
            <a href="https://giftup.app" target="_blank">log in</a> to your existing Gift Up! account
        </li>
        <li>
            Once inside your Gift Up! account,
            <a href="https://giftup.app/integrations/api-keys" target="_blank">get your API key</a>
        </li>
        <li>
            Copy & paste the provided Gift Up! API key below:
        </li>
        <li>
            <a href="https://giftup.app/">View your Gift Up! dashboard&nbsp;&nbsp;<i class="fa fa-external-link"></i></a>
        </li>
        <li>
            <a href="https://help.giftup.com/">Gift Up! help center&nbsp;&nbsp;<i class="fa fa-external-link"></i></a>
        </li>
    </ol>
</div>
