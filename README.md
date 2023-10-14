# system-analytic

A laravel package that simplifies the implementation of the analytics of your system based on the data in your DB

```php
use Kakaprodo\SystemAnalytic\AnalyticGate;

AnalyticGate::process([
    'analytic_type' => 'new-user-bar-chart',
    'scope_type' => 'this_week',// last_week, last_year,last_month,range_date, range_year ...
]);

```

## 1. Prerequisites

-   php >= 7
-   kakaprodo/custom-data package

## 2. Installation

```sh
 composer require kakaprodo/system-analytic
```

## 3. Publishing Skeleton

-   ### Config file

    ```sh
    php artisan system-analytic:config
    ```

    The above command will create the configuration file with name `system-analytic.php` in the config folder.

-   ### Skeleton files

    At this point you can open the `system-analytic.php` file in the config folder. From there you can review all the settings, and customize them according to your desire. Then run the bellow command to create the analytic `Hub`:

    ```sh
    php artisan system-analytic:install
    ```

After running the above command, a new folder will be created in your app/Http folder(by default) and in the app/Http/Requests folder. in the next section you are going to discover the magic that will be happening in these files. Put your shoes on 🤪

## 4. Your First Analytic class(handler)

### 1. Create a handler

To generate the analytic class, you will need to run the bellow command:

```sh
php artisan system-analytic:handler NewUserBarChart --bar-chart
```

A bellow handler will be created, now let's explain this:

```php
class NewUserBarChart extends BlockChart
{
    protected function boot()
    {
    }

    protected function query()
    {
        return DB::table('example')
    }

    protected function result(LazyCollection $groupedResult): AnalyticResponse
    {
        return $this->response($groupedResult->all());
    }
}
```

As you can see,we have three magic methods and very important to understand:

-   `boot`: the method that you can use as the construct of your handler
-   `query`: the core of your class, this method will return the value that the package will use to filter data based on the request scope.
-   `result`: Will return data on which all filters have been applied, and it is formatted based on the handler type(BarChart).

### 2. Define Basic query In the Handler

Now we have our handler, let's give it a sens, so that it can return the block chart data of new users of the system

```php
class NewUserBarChart extends BlockChart
{
    protected $scopeColumn = "users.created_at";

    protected $groupBy = "created_at";

    protected function boot()
    {
    }

    protected function query()
    {
        return DB::table('users')->orderBy('id');
    }

    protected function result(LazyCollection $groupedUsersByCreatedAt): AnalyticResponse
    {
        $result = $groupedUsersByCreatedAt->map(function ($users) {
            return $users->count();
        });

        return $this->response($result->all());
    }
}
```

Now we have our handler that returns number of the new users at each date. Note that you can make any logic of your choice to your query and that will work.

### 3. Register the handler class

Now you have your handler ready to be used. To register your handler you need to
open the `AnalyticHandlerRegister` class and register it under the `handlers` method. you will see this class in the analytic skeleton folder that we have created early in this doc.

```php
class AnalyticHandlerRegister extends AnalyticHandlerRegisterBase
{
    /**
     * register a key value array of your handlers,
     * where the key is the analytic_type and the value
     * is the actual handler
     */
    public static function handlers(): array
    {
        return [
            NewUserBarChart::type() => NewUserBarChart::class,
        ];
    }
}
```

You can see, we have used `NewUserBarChart::type()` , the `type` method will automatically return the kebak case of the name of your handler class. so if you think you will have multiple handlers with the same names, then you can specify your own names at the place of `NewUserBarChart::type()`.

### 3. Using the handler class

Now to call the handler , we will be using the class `AnalyticGate` and the registered name of your handler. Note that, the `AnalyticGate` is the gate to all registered handlers.

```php
use Kakaprodo\SystemAnalytic\AnalyticGate;

AnalyticGate::process([
    'analytic_type' => 'new-user-bar-chart',// NewUserBarChart::type()
    'scope_type' => 'this_week',// last_week, last_year,last_month ...
]);

```

VERY SIMPLE RIGHT ???🤪🤪🤪🤪🤪🤪🤪!!!

Now from the above code, we have used only two options among the list of options supported by the `AnalyticGate` class. here other options you can use

```php
 $options = [
    'scope_value' => date based on the scope_type,
    'scope_from_date' => date or dateTime based on your need,
    'scope_to_date' => date or dateTime based on your need,
    'search_value' => string or Array,
    'should_export' => Bool,
    'file_type' => string between [csv,xlsx],
    'selected_option' => string,
    'should_clear_cache' => Bool
 ];
```

Now i know, you are asking yourself why this dude didn't finish the documentation. 😜Man, i can not put everything here but trust me, i'm working on the full documentation and you will be the first to know about it once i finish. now high five 🖐✋🏾.
