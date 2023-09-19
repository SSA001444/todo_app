const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    setupNodeEvents(on, config) {

    },
  },
  env: {
    MAILISK_API_KEY: "DDR5TzVMtmy6Jz5GUKmADOsK2RjGY35tfk4X0ZFxZ5s",
    MAILISK_NAMESPACE: "osy8xi379v4x",
  },
});

