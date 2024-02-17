create table if not exists users(
  id text primary key not null,
  displayName text,
  bio text,
  username text not null unique,
  password blob not null,
  created integer not null
);

create table if not exists posts(
  id text primary key not null,
  parent text,
  author text,
  body text not null,
  created integer not null
);

drop view if exists v_posts;
create view v_posts
as
select
  p.*,
  u.displayName as authorDisplayName,
  u.username as authorUsername,
  count(c.id) as commentsCount
from posts p
left join posts c on c.parent = p.id
left join users u on u.id = p.author
group by p.id;