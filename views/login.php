<div style="max-inline-size:250px;margin-inline:auto">
  <h2>Login</h2>
  <form method="POST">
    <p style="color:orangered"><?= @$model['error'] ?></p>
    <label>Username
      <input name="username" type="text" maxlength="15" autocapitalize="off" required>
    </label>
    <label>Password
      <input name="password" type="password" maxlength="25" required>
    </label>
    <button type="submit">Login</button>
  </form>
  <br>
  <p>
    <a href="/signup">Don't have an account? Signup!</a>
  </p>
</div>