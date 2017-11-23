Global LiF defined core helper functions.

### A

- `bool app_debug()`

Check if application in debug model from configurations.

- `string app_env()`

Get current application environment from configurations.

### B

- `between($num, $start, $end)`

Compare a number is in the range of `$start` and `$end`.

- `build_cmds(string | array $cmds)`

Build a shell commands string from given `$cmds`.

- `build_cmds_with_env($cmds)`

Build a shell commands string from given `$cmds`, and prefixed with common used environment variables.

- `build_log_str($data, string $level = 'log') : string`

Build common log text string.

- `build_pdo_dsn($conn)`

Build PDO construct data source from configurations.

### C

- `string context()`

Get current PHP executing context.

### D

- `void dd(...$data)`

Try dump any given amount variables nicely and exit.

- `db(string $conn = null, bool $flush = false)`

Get sql database query builder instance.

- `db_conns()`

Get current database connections.

### E

- `void ee()`
 
Echo or print any amount given string variables and exit.

- `mixed exists($var, $idx = null)`

Check a variable, or item of an array, or attribute of an object and set forth exists in given source variable.

### F

- `bool fe(string $name)`

Check function existence, alias for `function_exists()`.

### G

- `string get_lif_ver()`

Get current LiF version string.

### H

### I

- `void init()`

Init LiF application, framework meta settings.

### J
### K
### L

- `void lif()`

Output LiF default hello world message in JSON format, and exit.

- `mixed legal_or(array | object &$data, array $rulesWithDefaults)`

Validate given `$data` by rules `$rulesWithDefaults`, and auto assign with  given default value if validation for that item has fails.

- `mixed legal_and(array | object $data, array $rulesWithDefaults)`

Validate given `$data` by rules `$rulesWithDefaults`, and auto assign validated item value to given variables passed by reference.

### M
### N
### O
### P

- `void pr(...$data)`

Try dump any amount given variables nicely and wouldn't exit.

### Q
### R

### S

- `void session_init()`

Init session related settings.

### T
### U
### V
### W
### X
### Y
### Z
