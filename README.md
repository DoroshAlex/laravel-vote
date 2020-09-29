# Laravel 5 Vote System

:tada: This package helps you to add user based vote system to your model.

>

## Installation

You can install the package using Composer:

```sh
$ composer require dorosh-alex/laravel-vote -vvv
```

Then add the service provider to `config/app.php`:

```php
DoroshAlex\LaravelVote\VoteServiceProvider::class
```

Publish the migrations file:

```sh
$ php artisan vendor:publish --provider="DoroshAlex\LaravelVote\VoteServiceProvider" --tag="migrations"
```

Finally, use VoteTrait in User model:

```php
use DoroshAlex\LaravelVote\Vote;

class User extends Model
{
    use Vote;
}
```

Or use CanBeVoted in Comment model:

```php
use DoroshAlex\LaravelVote\CanBeVoted;

class Comment extends Model
{
    use CanBeVoted;

    protected $vote = User::class;
}
```

## Usage

### For User model

#### Up vote a comment or comments

```php
$comment = Comment::find(1);

$user->upVote($comment);
```

### Down vote a comment or comments

```php
$comment = Comment::find(1);

$user->downVote($comment);
```

#### Cancel vote a comment or comments

```php
$comment = Comment::find(1);

$user->cancelVote($comment);
```

#### Get user has voted comment items

```php
$user->votedItems(Comment::class)->get();
```

#### Check if user has up or down vote

```
$comment = Comment::find(1);

$user->hasVoted($comment);
```

#### Check if user has up vote

```
$comment = Comment::find(1);

$user->hasUpVoted($comment);
```

#### Check if user has down vote

```
$comment = Comment::find(1);

$user->hasDownVoted($comment);
```

### For Comment model

#### Get comment voters

```php
$comment->voters();
```

#### Count comment voters

```php
$comment->countVoters();
```

#### Count comment up voters

```php
$comment->countUpVoters();
```

#### Count comment down voters

```php
$comment->countDownVoters();
```

#### Check if voted by

```php
$comment->isVotedBy(1);
```

## Reference

[laravel-follow](https://github.com/overtrue/laravel-follow)

## License

MIT
