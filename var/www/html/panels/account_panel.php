<h2>Change Password</h2>
<form method="POST" action="account.php">
    <div class="form-label">Current Password:</div>
    <input class="form-input small-input" type="password" name="old-passwd" placeholder="Current password...">
    <br>
    <div class="form-label">New Password:</div>
    <input class="form-input small-input" type="password" name="new-passwd" placeholder="Enter new password...">
    <br>
    <div class="form-label">Confirm New Password:</div>
    <input class="form-input small-input" type="password" name="new-passwd-confirm" placeholder="Repeat password...">
    <input class="form-submit" type="submit" name="submit" value="Change Password">
</form>

<h2>Add SSH Key</h2>
<form method="POST" action="account.php">
    <label>SSH Public Key:</label><br>
    <textarea name="ssh-key" class="ssh-entry" id="connect-new-user-ssh-entry" autocomplete="off" placeholder="Enter an SSH public key..."></textarea>
    <input class="file-selector" type="file" id="add-ssh" name="add-ssh-file">
    <br>
    <input class="form-submit" type="submit" name="submit" value="Add SSH Key">
<form>

<h2>Setup Git SSH</h2>
<p>Copy this into ~/.ssh/config</p>
<div class="code-block" id="ssh-setup-code"># [GITSERVER]
Host gitserver
	Hostname localhost
	User git
	IdentityFile ~/.ssh/id_rsa
	Port 2222
# [END GITSERVER]
</div>
<p>If your SSH public/private key pair is in a different location, modify the path given for IdentityFile accordingly.</p><br><br>
