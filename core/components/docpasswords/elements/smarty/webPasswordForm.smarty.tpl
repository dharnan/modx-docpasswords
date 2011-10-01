<div id="docPassword">
    <p>This page is password protected.<br/>Please enter the password to continue.</p>
    <span class="docPasswordFormError" style="{$style}">{$message}</span>
    <form id="docPasswordForm" method="POST" action="">
        <div>
           <label>Password:</label><input type="text" name="dPassword" value="" class="textfield" />
        </div>
        <div>
            <input type="submit" name="dSubmit" value="Submit" class="submit" />
        </div>
    </form>
</div>