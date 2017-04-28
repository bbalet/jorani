# Phony changelog

## 0.14.7 (2017-04-22)

- **[FIXED]** The "last error" state is now cleared when using the feature
  detector ([#209], [#214]).

[#209]: https://github.com/eloquent/phony/issues/209
[#214]: https://github.com/eloquent/phony/pull/214

## 0.14.6 (2017-01-03)

- **[FIXED]** Partial mocks of abstract functions with return types now work as
  intended ([#212]).
- **[FIXED]** Fixed regression of [#203] and [#204].

[#212]: https://github.com/eloquent/phony/issues/212

## 0.14.5 (2016-12-07)

- **[FIXED]** Mock handle substitution fixed for `threw()` and
  `receivedException()` verifications ([#211]).
- **[IMPROVED]** Inline exporter now uses tilde (`~`) to indicate truncated
  content ([#210]).
- **[IMPROVED]** Refactored the feature detector.

[#210]: https://github.com/eloquent/phony/issues/210
[#211]: https://github.com/eloquent/phony/issues/211

## 0.14.4 (2016-11-23)

- **[FIXED]** Fixed mocking of classes with `self` return type ([#208]).

[#208]: https://github.com/eloquent/phony/pull/208

## 0.14.3 (2016-11-13)

- **[FIXED]** Suppress `posix_isatty` warnings that occur when PHPUnit process
  isolation is in use ([#207] - thanks [@keksa]).

[#207]: https://github.com/eloquent/phony/pull/207

## 0.14.2 (2016-11-10)

- **[FIXED]** Fixed nullable return type support for newer PHP 7.1 release
  candidates ([#206]).

[#206]: https://github.com/eloquent/phony/issues/206

## 0.14.1 (2016-11-04)

- **[FIXED]** Checking for nullable type support no longer causes fatal errors
  under HHVM ([#203], [#204] - thanks [@shadowhand]).

[#203]: https://github.com/eloquent/phony/issues/203
[#204]: https://github.com/eloquent/phony/pull/204

## 0.14.0 (2016-09-30)

- **[BC BREAK]** Removed `calledOn()` ([#197]).
- **[IMPROVED]** Generator spies and iterable spies are now substituted in
  argument matching and verification ([#193]).
- **[IMPROVED]** Documentation improvements ([#191], [#192], [#198]).

[#191]: https://github.com/eloquent/phony/issues/191
[#192]: https://github.com/eloquent/phony/issues/192
[#193]: https://github.com/eloquent/phony/issues/193
[#197]: https://github.com/eloquent/phony/issues/197
[#198]: https://github.com/eloquent/phony/issues/198

## 0.13.5 (2016-09-20)

- **[IMPROVED]** Support for PHP 7.1 `iterable` pseudo-type ([#195]).
- **[IMPROVED]** Support for PHP 7.1 `void` pseudo-type ([#195]).
- **[IMPROVED]** Support for PHP 7.1 nullable types ([#195]).
- **[IMPROVED]** Disallow ASCII delete in symbol names, matching the change in
  PHP 7.1 ([#195]).

[#195]: https://github.com/eloquent/phony/issues/195

## 0.13.4 (2016-08-17)

- **[IMPROVED]** Documentation improvements ([#186], [#188], [#189], [#190]).

[#186]: https://github.com/eloquent/phony/issues/186
[#188]: https://github.com/eloquent/phony/issues/188
[#189]: https://github.com/eloquent/phony/issues/189
[#190]: https://github.com/eloquent/phony/issues/190

## 0.13.3 (2016-08-07)

- **[FIXED]** Verification results now return call verifiers instead of
  unwrapped calls ([#185]).

[#185]: https://github.com/eloquent/phony/issues/185

## 0.13.2 (2016-08-05)

- **[FIXED]** Fixed mocking of destructors ([#183]).

[#183]: https://github.com/eloquent/phony/issues/183

## 0.13.1 (2016-08-02)

- **[IMPROVED]** Improved support for mocking many problematic classes ([#182]).

[#182]: https://github.com/eloquent/phony/issues/182

## 0.13.0 (2016-07-15)

- **[BC BREAK]** Renamed `$handle->mock()` to `$handle->get()` ([#180]).
- **[BC BREAK]** Removed `verify()` and `verifyStatic()` ([#179]).
- **[BC BREAK]** Removed magic calls from mock handles. All stubbing must now
  use `with()`, and all call argument verification must now use `calledWith()`
  ([#179]).
- **[IMPROVED]** Improved exporting of mock handles, stubs, spies, and closures
  ([#177]).

[#177]: https://github.com/eloquent/phony/issues/177
[#179]: https://github.com/eloquent/phony/issues/179
[#180]: https://github.com/eloquent/phony/issues/180

## 0.12.0 (2016-07-13)

- **[BC BREAK]** Replaced the term "traversable" with "iterable". Any function
  or method with "traversable" in the name has also been renamed accordingly
  ([#164]).
- **[BC BREAK]** Stubs now return empty values by default, instead of forwarding
  ([#174]).

[#164]: https://github.com/eloquent/phony/issues/164
[#174]: https://github.com/eloquent/phony/issues/174

## 0.11.0 (2016-07-12)

- **[NEW]** Implemented `stubGlobal()` ([#163]).
- **[NEW]** Implemented `spyGlobal()` ([#175]).
- **[IMPROVED]** Traversable spies now implement `ArrayAccess` and `Countable`
  ([#165]).

[#163]: https://github.com/eloquent/phony/issues/163
[#165]: https://github.com/eloquent/phony/issues/165
[#175]: https://github.com/eloquent/phony/issues/175

## 0.10.2 (2016-07-06)

- **[IMPROVED]** Complete overhaul of verification output, with improvements to
  the output of all verifications ([#161], [#170]).
- **[IMPROVED]** Improved exporting of closures under HHVM ([#166]).
- **[FIXED]** Fixed `calledOn()` behavior ([#160]).
- **[FIXED]** Fixed verification output under Windows ([#167]).
- **[FIXED]** Error reporting is now correctly restored in all cases ([#168]).
- **[FIXED]** Fixed the recording of static magic method calls ([#169]).

[#160]: https://github.com/eloquent/phony/issues/160
[#161]: https://github.com/eloquent/phony/issues/161
[#166]: https://github.com/eloquent/phony/issues/166
[#167]: https://github.com/eloquent/phony/issues/167
[#168]: https://github.com/eloquent/phony/issues/168
[#169]: https://github.com/eloquent/phony/issues/169
[#170]: https://github.com/eloquent/phony/issues/170

## 0.10.1 (2016-06-07)

- **[FIXED]** Fixed magic self parameters in ad-hoc mock definitions ([#158]).
- **[FIXED]** Fixed mocking of internal classes that implement `Traversable`
  and other unimplementable interfaces directly ([#159]).

[#158]: https://github.com/eloquent/phony/issues/158
[#159]: https://github.com/eloquent/phony/issues/159

## 0.10.0 (2016-05-25)

- **[BC BREAK]** Removed `produced()`, `producedAll()`, `received()`,
  `receivedException()` and associated check verification methods in both the
  spy and call APIs. These methods are replaced by generator and traversable
  verifiers ([#125]).
- **[BC BREAK]** Usage of `setsArgument()`, `callsArgument()`,
  `callsArgumentWith()`, and `returnsArgument()` can now result in exceptions
  when a specified argument is undefined at call time ([#136]).
- **[BC BREAK]** Removed `arguments()` and `argument()` from all event
  collections, in favor of using `firstCall()` et al. followed by the
  corresponding method on the call API ([#146]).
- **[NEW]** Implemented generator and traversable verifiers ([#102], [#115],
  [#125]).
- **[NEW]** Implemented `responded()` and `completed()` verifications ([#125]).
- **[NEW]** Mocks as default stub return values for arbitrary object return
  types ([#149], [#150]).
- **[FIXED]** Fixed exception when stubbing functions with return values
  ([#147]).
- **[FIXED]** Fixed mocking of `Traversable` ([#152]).
- **[FIXED]** Fixed mocking of `DateTimeInterface` ([#153]).
- **[FIXED]** Fixed mocking of classes with final constructors ([#154]).

[#102]: https://github.com/eloquent/phony/issues/102
[#115]: https://github.com/eloquent/phony/issues/115
[#125]: https://github.com/eloquent/phony/issues/125
[#136]: https://github.com/eloquent/phony/issues/136
[#146]: https://github.com/eloquent/phony/issues/146
[#147]: https://github.com/eloquent/phony/issues/147
[#149]: https://github.com/eloquent/phony/issues/149
[#150]: https://github.com/eloquent/phony/issues/150
[#152]: https://github.com/eloquent/phony/issues/152
[#153]: https://github.com/eloquent/phony/issues/153
[#154]: https://github.com/eloquent/phony/issues/154

## 0.9.0 (2016-04-27)

- **[NEW]** Implemented generator stubs ([#11], [#140], [#144]).
- **[IMPROVED]** More default values for built-in return types ([#138], [#139]).
- **[FIXED]** Omitted arguments no longer pass for `any()` matcher ([#137]).
- **[FIXED]** Fixed memory leak under PHP 7 ([#143]).

[#11]: https://github.com/eloquent/phony/issues/11
[#137]: https://github.com/eloquent/phony/issues/137
[#138]: https://github.com/eloquent/phony/issues/138
[#139]: https://github.com/eloquent/phony/pull/139
[#140]: https://github.com/eloquent/phony/issues/140
[#143]: https://github.com/eloquent/phony/issues/143
[#144]: https://github.com/eloquent/phony/issues/144

## 0.8.0 (2016-02-12)

- **[BC BREAK]** Mocking functions now accept ad hoc definitions in the `$types`
  argument, hence the `$definition` argument has been removed from `mock()`,
  `partialMock()`, and `mockBuilder()` ([#117]).
- **[BC BREAK]** Mocking functions `mock()`, `partialMock()`, and
  `mockBuilder()` no longer accept the `$className` argument. Custom class names
  can still be used via `named()` on mock builders ([#117]).
- **[BC BREAK]** Mocking functions `mock()`, `partialMock()`, and
  `mockBuilder()` no longer accept reflection classes or mock builders in the
  `$types` argument ([#117]).
- **[BC BREAK]** Mock definition values can no longer be generic objects
  ([#117]).
- **[BC BREAK]** Removed the `$useGeneratorSpies` and `$useTraversableSpies`
  argument from both `spy()` and `stub()` ([#123]).
- **[BC BREAK]** Removed the `$self` and `$defaultAnswerCallback` arguments from
  `stub()` ([#123]).
- **[BC BREAK]** Rewrite and rename of mock builder API methods for creating
  mocks ([#103]).
- **[BC BREAK]** Renamed "proxy" to "handle" in line with documentation
  ([#133]).
- **[BC BREAK]** Replaced `reset()` with `stopRecording()` and
  `startRecording()` ([#99]).
- **[BC BREAK]** Event no longer implements event collection ([#134]).
- **[NEW]** Mock builders can now be copied via the `clone` operator ([#101]).
- **[NEW]** Implemented `proxy()` on mock handles ([#39]).
- **[IMPROVED]** Made API setter style methods fluent ([#98]).
- **[IMPROVED]** Instance handles are automatically adapted when stubbing and
  verifying ([#126]).
- **[IMPROVED]** Added checks for unused stub criteria ([#126]).
- **[IMPROVED]** Default answer callbacks are now a first-class concept for
  mocks ([#90]).
- **[IMPROVED]** Stubs now return valid default values for most "in built"
  return type declarations ([#109]).
- **[IMPROVED]** Verification results (event collections) now implement
  `firstEvent()` and `lastEvent()` ([#97]).
- **[IMPROVED]** Requesting the return value or exception of a call that has not
  responded now throws an exception ([#92]).
- **[FIXED]** Fixed bug when mocking traits with magic call methods ([#127]).
- **[FIXED]** Mocking, and calling of return-by-reference methods no longer
  causes errors to be emitted ([#105]).
- **[FIXED]** Ad-hoc mocks that differ only by function body no longer result in
  re-use of the same mock class ([#131]).

[#39]: https://github.com/eloquent/phony/issues/39
[#90]: https://github.com/eloquent/phony/issues/90
[#92]: https://github.com/eloquent/phony/issues/92
[#97]: https://github.com/eloquent/phony/issues/97
[#98]: https://github.com/eloquent/phony/issues/98
[#99]: https://github.com/eloquent/phony/issues/99
[#101]: https://github.com/eloquent/phony/issues/101
[#103]: https://github.com/eloquent/phony/issues/103
[#105]: https://github.com/eloquent/phony/issues/105
[#109]: https://github.com/eloquent/phony/issues/109
[#117]: https://github.com/eloquent/phony/issues/117
[#123]: https://github.com/eloquent/phony/issues/123
[#126]: https://github.com/eloquent/phony/issues/126
[#127]: https://github.com/eloquent/phony/issues/127
[#131]: https://github.com/eloquent/phony/issues/131
[#133]: https://github.com/eloquent/phony/issues/133
[#134]: https://github.com/eloquent/phony/issues/134

## 0.7.0 (2015-12-20)

- **[NEW]** Implemented `firstCall()` and `lastCall()` ([#93]).
- **[IMPROVED]** Support for PHP 7 engine error exceptions ([#119]).
- **[IMPROVED]** Support for PHP 7 scalar type hints ([#106] - thanks
  [@jmalloc]).
- **[IMPROVED]** Support for PHP 7 return type declarations ([#104], [#108] -
  thanks [@jmalloc]).
- **[IMPROVED]** Support for PHP 7 methods names that match tokens ([#110] -
  thanks [@jmalloc]).
- **[IMPROVED]** Partial support for PHP 7 generator returns ([#111] - thanks
  [@jmalloc]).
- **[IMPROVED]** Tidied up many interfaces and doc blocks.

[#93]: https://github.com/eloquent/phony/issues/93
[#104]: https://github.com/eloquent/phony/issues/104
[#106]: https://github.com/eloquent/phony/issues/106
[#108]: https://github.com/eloquent/phony/issues/108
[#110]: https://github.com/eloquent/phony/issues/110
[#111]: https://github.com/eloquent/phony/issues/111
[#119]: https://github.com/eloquent/phony/issues/119

## 0.6.4 (2015-12-19)

- **[FIXED]** Simplified method resolution rules. Fixes issue when mocking
  interfaces and traits ([#112]).

[#112]: https://github.com/eloquent/phony/issues/112

## 0.6.3 (2015-12-18)

- **[FIXED]** Fixed custom mocks with invocable objects as method
  implementations ([#113]).
- **[FIXED]** Fixed required, but nullable parameters in function signatures
  ([#114]).

[#113]: https://github.com/eloquent/phony/issues/113
[#114]: https://github.com/eloquent/phony/issues/114

## 0.6.2 (2015-12-16)

- **[IMPROVED]** Huge additions to the documentation ([#85], [#88]).
- **[FIXED]** Fixed error with EqualToMatcher when comparing object to
  non-object (#100).

[#85]: https://github.com/eloquent/phony/issues/85
[#88]: https://github.com/eloquent/phony/issues/88
[#100]: https://github.com/eloquent/phony/issues/100

## 0.6.1 (2015-11-16)

- **[IMPROVED]** Mock instances labels are now compared by the equal to matcher
  (#91).
- **[IMPROVED]** The inline exporter now outputs mock labels ([#91]).

[#91]: https://github.com/eloquent/phony/issues/91

## 0.6.0 (2015-11-12)

- **[NEW]** Support for stub default answer callbacks.
- **[FIXED]** Fixed full mock default answer bug (#89).

[#89]: https://github.com/eloquent/phony/issues/89

## 0.5.2 (2015-11-05)

- **[FIXED]** Fixed stripping of exception xdebug message in exporter and equal
  to matcher ([#87]).
- **[DOCUMENTATION]** Added documentation.

[#87]: https://github.com/eloquent/phony/pull/87

## 0.5.1 (2015-10-22)

- **[IMPROVED]** Prevent exporter and matcher from traversing into mock
  internals ([#82]).
- **[FIXED]** Fixed assertion recording bug with `noInteraction()` ([#83]).

[#82]: https://github.com/eloquent/phony/issues/82
[#83]: https://github.com/eloquent/phony/issues/83

## 0.5.0 (2015-10-20)

- **[BC BREAK]** Removed `fullMock()`, changed `mock()` to create full mocks,
  and added `partialMock()` for creating partial mocks ([#73]).

[#73]: https://github.com/eloquent/phony/issues/73

## 0.4.0 (2015-10-20)

- **[IMPROVED]** Implemented new 'equal to' matcher ([#70] - thanks [@jmalloc]).
- **[IMPROVED]** Improved rendering of assertion failure messages ([#71]).
- **[IMPROVED]** String messages now allowed by `throws()` ([#76]).
- **[FIXED]** Fixed magic method mocking bug ([#74]).
- **[FIXED]** Fixed mocking of exceptions under HHVM ([#75]).
- **[FIXED]** Attempting to stub a final method now throws an exception ([#77]).

[#70]: https://github.com/eloquent/phony/issues/70
[#71]: https://github.com/eloquent/phony/issues/71
[#74]: https://github.com/eloquent/phony/issues/74
[#75]: https://github.com/eloquent/phony/issues/75
[#76]: https://github.com/eloquent/phony/issues/76
[#77]: https://github.com/eloquent/phony/issues/77

## 0.3.0 (2015-07-22)

- **[NEW]** PHP 7 support.
- **[NEW]** Support for variadic functions ([#64] - thanks [@jmalloc]).
- **[NEW]** Implemented `eventAt()` and `callAt()` for verification results
  ([#17]).
- **[NEW]** Implemented `Call::argument()` ([#56]).
- **[NEW]** Implemented `MockBuilder::source()` for easier debugging ([#45]).
- **[NEW]** Implemented `anyOrder()` ([#60]).
- **[IMPROVED]** Vast improvements to verification failure output ([#66]).
- **[IMPROVED]** Allow use of phonySelf parameter everywhere ([#63]).
- **[IMPROVED]** Optimizations to the matcher driver system ([#67]).
- **[IMPROVED]** Optimizations to the equal to matcher ([#69]).
- **[IMPROVED]** Calls to eval() no longer use @ suppression.

[#17]: https://github.com/eloquent/phony/issues/17
[#45]: https://github.com/eloquent/phony/issues/45
[#56]: https://github.com/eloquent/phony/issues/56
[#60]: https://github.com/eloquent/phony/issues/60
[#63]: https://github.com/eloquent/phony/issues/63
[#64]: https://github.com/eloquent/phony/pull/64
[#66]: https://github.com/eloquent/phony/issues/66
[#67]: https://github.com/eloquent/phony/issues/67
[#69]: https://github.com/eloquent/phony/issues/69

## 0.2.1 (2015-02-28)

- **[FIXED]** Cardinality checks for `received()` now work as expected ([#54]).
- **[FIXED]** Methods names are correctly treated as case-insensitive ([#58]).
- **[FIXED]** Can mock an interface that extends `Traversable` ([#59]).
- **[FIXED]** Calling of trait constructors ([#61]).

[#54]: https://github.com/eloquent/phony/issues/54
[#58]: https://github.com/eloquent/phony/issues/58
[#59]: https://github.com/eloquent/phony/issues/59
[#61]: https://github.com/eloquent/phony/issues/61

## 0.2.0 (2014-11-18)

- **[BC BREAK]** Renamed IDs to labels.
- **[NEW]** Verify no interaction ([#27]).
- **[NEW]** Manual constructor calling ([#36], [#46]).
- **[NEW]** Setters for labels ([#35]).
- **[NEW]** [Peridot] integration ([#50]).
- **[NEW]** [Pho] integration ([#51]).
- **[NEW]** [SimpleTest] integration ([#20]).
- **[FIXED]** Trait mocking is now working ([#42], [#49]).
- **[FIXED]** Stubbing interface bugs ([#53]).
- **[IMPROVED]** Better assertion messages ([#41]).
- **[IMPROVED]** Generator spies under HHVM ([#29]).
- **[IMPROVED]** Better mock definition equality checking ([#47]).
- **[IMPROVED]** Throw an exception when passing the wrong types to `inOrder()`
  ([#52]).
- **[IMPROVED]** Magic 'self' parameter detection ([#48]).

[peridot]: https://github.com/peridot-php/peridot
[pho]: https://github.com/danielstjules/pho
[simpletest]: https://github.com/lox/simpletest
[#20]: https://github.com/eloquent/phony/issues/20
[#27]: https://github.com/eloquent/phony/issues/27
[#29]: https://github.com/eloquent/phony/issues/29
[#35]: https://github.com/eloquent/phony/issues/35
[#36]: https://github.com/eloquent/phony/issues/36
[#41]: https://github.com/eloquent/phony/issues/41
[#42]: https://github.com/eloquent/phony/issues/42
[#46]: https://github.com/eloquent/phony/issues/46
[#47]: https://github.com/eloquent/phony/issues/47
[#48]: https://github.com/eloquent/phony/issues/48
[#49]: https://github.com/eloquent/phony/issues/49
[#50]: https://github.com/eloquent/phony/issues/50
[#51]: https://github.com/eloquent/phony/issues/51
[#52]: https://github.com/eloquent/phony/issues/52
[#53]: https://github.com/eloquent/phony/issues/53

## 0.1.1 (2014-10-26)

- **[IMPROVED]** Performance improvements when repeatedly mocking the same
  types ([#44]).
- **[IMPROVED]** Performance improvements when mocking large classes ([#44]).
- **[IMPROVED]** Improved exception message when mocking an undefined type
  ([#40]).

[#40]: https://github.com/eloquent/phony/issues/40
[#44]: https://github.com/eloquent/phony/issues/44

## 0.1.0 (2014-10-21)

- **[NEW]** Initial implementation.

[@jmalloc]: https://github.com/jmalloc
[@keksa]: https://github.com/keksa
[@shadowhand]: https://github.com/shadowhand
