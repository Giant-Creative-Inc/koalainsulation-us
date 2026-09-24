const test = require("node:test");
const assert = require("node:assert/strict");

const locationSwitch = require("../wp-content/themes/bricks/assets/js/cross-border-location-switch.js");

test("US site recognizes Canadian postal codes with or without a space", () => {
  assert.equal(locationSwitch.isCanadianPostalCode("M5V 3A8"), true);
  assert.equal(locationSwitch.isCanadianPostalCode("k1a0b1"), true);
  assert.equal(locationSwitch.isCanadianPostalCode("90210"), false);
});

test("US site redirects only when the visitor accepts", () => {
  assert.equal(
    locationSwitch.getSwitchUrl("M5V 3A8", "US", true),
    "https://koalainsulation.com/ca/locations/"
  );
  assert.equal(locationSwitch.getSwitchUrl("M5V 3A8", "US", false), null);
  assert.equal(locationSwitch.getSwitchUrl("90210", "US", true), null);
});

test("US site supplies branded Canadian switch-dialog copy", () => {
  assert.deepEqual(locationSwitch.getDialogCopy("US"), {
    title: "Switch to the Canadian site?",
    message: "It looks like you entered a Canadian postal code.",
    visitLabel: "Visit Canadian Site",
    stayLabel: "Stay on U.S. Site",
  });
});
