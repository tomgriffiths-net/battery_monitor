# battery_monitor
battery_monitor is a PHP-CLI package that monitors a computers battery state and shuts down the computer after an amount of seconds.

# Settings
- **shutdownTimer**: Integer, default of 600, specifies the number of seconds to wait after the computer is in the discharging state to run the shutdown command.
- **shutdownCommand**: String, default of "shutdown /s /f /t 0", specifies the command to be run to shutdown the computer.
- **batteryInterval**: Integer, default of 30, specifies the interval of checking the battery state.

# Commands
Base command tests the isDischarging function.

- **loop**: Monitors the battery continuously.

# Functions
- **isDischarging():bool**: Checks weather the battery is in the discharging state, returns true when the battery is discharging and false otherwise.