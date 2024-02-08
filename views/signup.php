<div style="max-inline-size:250px;margin-inline:auto">
  <h2>Signup</h2>
  <form method="POST">
    <p style="color:orangered"><?= @$err ?></p>
    <label>Display Name
      <input name="displayName" type="text" maxlength="25" required>
    </label>
    <label>Username
      <input name="username" type="text" maxlength="15" pattern="\S+" autocapitalize="off" required>
    </label>
    <label>Password
      <input name="password" type="password" maxlength="25" required>
    </label>
    <button type="submit">Signup</button>
  </form>
  <br>
  <p>
    <a href="/login">Already have an account? Login!</a>
  </p>
</div>