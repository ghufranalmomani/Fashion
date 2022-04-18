<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Argument\BoundArgument;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
class Definition
{
 private const DEFAULT_DEPRECATION_TEMPLATE = 'The "%service_id%" service is deprecated. You should stop using it, as it will be removed in the future.';
 private $class;
 private $file;
 private $factory;
 private $shared = \true;
 private $deprecated = \false;
 private $deprecationTemplate;
 private $properties = [];
 private $calls = [];
 private $instanceof = [];
 private $autoconfigured = \false;
 private $configurator;
 private $tags = [];
 private $public = \true;
 private $private = \true;
 private $synthetic = \false;
 private $abstract = \false;
 private $lazy = \false;
 private $decoratedService;
 private $autowired = \false;
 private $changes = [];
 private $bindings = [];
 private $errors = [];
 protected $arguments = [];
 public $innerServiceId;
 public $decorationOnInvalid;
 public function __construct($class = null, array $arguments = [])
 {
 if (null !== $class) {
 $this->setClass($class);
 }
 $this->arguments = $arguments;
 }
 public function getChanges()
 {
 return $this->changes;
 }
 public function setChanges(array $changes)
 {
 $this->changes = $changes;
 return $this;
 }
 public function setFactory($factory)
 {
 $this->changes['factory'] = \true;
 if (\is_string($factory) && \str_contains($factory, '::')) {
 $factory = \explode('::', $factory, 2);
 } elseif ($factory instanceof Reference) {
 $factory = [$factory, '__invoke'];
 }
 $this->factory = $factory;
 return $this;
 }
 public function getFactory()
 {
 return $this->factory;
 }
 public function setDecoratedService($id, $renamedId = null, $priority = 0)
 {
 if ($renamedId && $id === $renamedId) {
 throw new InvalidArgumentException(\sprintf('The decorated service inner name for "%s" must be different than the service name itself.', $id));
 }
 $invalidBehavior = 3 < \func_num_args() ? (int) \func_get_arg(3) : ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
 $this->changes['decorated_service'] = \true;
 if (null === $id) {
 $this->decoratedService = null;
 } else {
 $this->decoratedService = [$id, $renamedId, (int) $priority];
 if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE !== $invalidBehavior) {
 $this->decoratedService[] = $invalidBehavior;
 }
 }
 return $this;
 }
 public function getDecoratedService()
 {
 return $this->decoratedService;
 }
 public function setClass($class)
 {
 if ($class instanceof Parameter) {
 @\trigger_error(\sprintf('Passing an instance of %s as class name to %s in deprecated in Symfony 4.4 and will result in a TypeError in 5.0. Please pass the string "%%%s%%" instead.', Parameter::class, __CLASS__, (string) $class), \E_USER_DEPRECATED);
 }
 if (null !== $class && !\is_string($class)) {
 @\trigger_error(\sprintf('The class name passed to %s is expected to be a string. Passing a %s is deprecated in Symfony 4.4 and will result in a TypeError in 5.0.', __CLASS__, \is_object($class) ? \get_class($class) : \gettype($class)), \E_USER_DEPRECATED);
 }
 $this->changes['class'] = \true;
 $this->class = $class;
 return $this;
 }
 public function getClass()
 {
 return $this->class;
 }
 public function setArguments(array $arguments)
 {
 $this->arguments = $arguments;
 return $this;
 }
 public function setProperties(array $properties)
 {
 $this->properties = $properties;
 return $this;
 }
 public function getProperties()
 {
 return $this->properties;
 }
 public function setProperty($name, $value)
 {
 $this->properties[$name] = $value;
 return $this;
 }
 public function addArgument($argument)
 {
 $this->arguments[] = $argument;
 return $this;
 }
 public function replaceArgument($index, $argument)
 {
 if (0 === \count($this->arguments)) {
 throw new OutOfBoundsException('Cannot replace arguments if none have been configured yet.');
 }
 if (\is_int($index) && ($index < 0 || $index > \count($this->arguments) - 1)) {
 throw new OutOfBoundsException(\sprintf('The index "%d" is not in the range [0, %d].', $index, \count($this->arguments) - 1));
 }
 if (!\array_key_exists($index, $this->arguments)) {
 throw new OutOfBoundsException(\sprintf('The argument "%s" doesn\'t exist.', $index));
 }
 $this->arguments[$index] = $argument;
 return $this;
 }
 public function setArgument($key, $value)
 {
 $this->arguments[$key] = $value;
 return $this;
 }
 public function getArguments()
 {
 return $this->arguments;
 }
 public function getArgument($index)
 {
 if (!\array_key_exists($index, $this->arguments)) {
 throw new OutOfBoundsException(\sprintf('The argument "%s" doesn\'t exist.', $index));
 }
 return $this->arguments[$index];
 }
 public function setMethodCalls(array $calls = [])
 {
 $this->calls = [];
 foreach ($calls as $call) {
 $this->addMethodCall($call[0], $call[1], $call[2] ?? \false);
 }
 return $this;
 }
 public function addMethodCall($method, array $arguments = [])
 {
 if (empty($method)) {
 throw new InvalidArgumentException('Method name cannot be empty.');
 }
 $this->calls[] = 2 < \func_num_args() && \func_get_arg(2) ? [$method, $arguments, \true] : [$method, $arguments];
 return $this;
 }
 public function removeMethodCall($method)
 {
 foreach ($this->calls as $i => $call) {
 if ($call[0] === $method) {
 unset($this->calls[$i]);
 }
 }
 return $this;
 }
 public function hasMethodCall($method)
 {
 foreach ($this->calls as $call) {
 if ($call[0] === $method) {
 return \true;
 }
 }
 return \false;
 }
 public function getMethodCalls()
 {
 return $this->calls;
 }
 public function setInstanceofConditionals(array $instanceof)
 {
 $this->instanceof = $instanceof;
 return $this;
 }
 public function getInstanceofConditionals()
 {
 return $this->instanceof;
 }
 public function setAutoconfigured($autoconfigured)
 {
 $this->changes['autoconfigured'] = \true;
 $this->autoconfigured = $autoconfigured;
 return $this;
 }
 public function isAutoconfigured()
 {
 return $this->autoconfigured;
 }
 public function setTags(array $tags)
 {
 $this->tags = $tags;
 return $this;
 }
 public function getTags()
 {
 return $this->tags;
 }
 public function getTag($name)
 {
 return $this->tags[$name] ?? [];
 }
 public function addTag($name, array $attributes = [])
 {
 $this->tags[$name][] = $attributes;
 return $this;
 }
 public function hasTag($name)
 {
 return isset($this->tags[$name]);
 }
 public function clearTag($name)
 {
 unset($this->tags[$name]);
 return $this;
 }
 public function clearTags()
 {
 $this->tags = [];
 return $this;
 }
 public function setFile($file)
 {
 $this->changes['file'] = \true;
 $this->file = $file;
 return $this;
 }
 public function getFile()
 {
 return $this->file;
 }
 public function setShared($shared)
 {
 $this->changes['shared'] = \true;
 $this->shared = (bool) $shared;
 return $this;
 }
 public function isShared()
 {
 return $this->shared;
 }
 public function setPublic($boolean)
 {
 $this->changes['public'] = \true;
 $this->public = (bool) $boolean;
 $this->private = \false;
 return $this;
 }
 public function isPublic()
 {
 return $this->public;
 }
 public function setPrivate($boolean)
 {
 $this->private = (bool) $boolean;
 return $this;
 }
 public function isPrivate()
 {
 return $this->private;
 }
 public function setLazy($lazy)
 {
 $this->changes['lazy'] = \true;
 $this->lazy = (bool) $lazy;
 return $this;
 }
 public function isLazy()
 {
 return $this->lazy;
 }
 public function setSynthetic($boolean)
 {
 $this->synthetic = (bool) $boolean;
 return $this;
 }
 public function isSynthetic()
 {
 return $this->synthetic;
 }
 public function setAbstract($boolean)
 {
 $this->abstract = (bool) $boolean;
 return $this;
 }
 public function isAbstract()
 {
 return $this->abstract;
 }
 public function setDeprecated($status = \true, $template = null)
 {
 if (null !== $template) {
 if (\preg_match('#[\\r\\n]|\\*/#', $template)) {
 throw new InvalidArgumentException('Invalid characters found in deprecation template.');
 }
 if (!\str_contains($template, '%service_id%')) {
 throw new InvalidArgumentException('The deprecation template must contain the "%service_id%" placeholder.');
 }
 $this->deprecationTemplate = $template;
 }
 $this->changes['deprecated'] = \true;
 $this->deprecated = (bool) $status;
 return $this;
 }
 public function isDeprecated()
 {
 return $this->deprecated;
 }
 public function getDeprecationMessage($id)
 {
 return \str_replace('%service_id%', $id, $this->deprecationTemplate ?: self::DEFAULT_DEPRECATION_TEMPLATE);
 }
 public function setConfigurator($configurator)
 {
 $this->changes['configurator'] = \true;
 if (\is_string($configurator) && \str_contains($configurator, '::')) {
 $configurator = \explode('::', $configurator, 2);
 } elseif ($configurator instanceof Reference) {
 $configurator = [$configurator, '__invoke'];
 }
 $this->configurator = $configurator;
 return $this;
 }
 public function getConfigurator()
 {
 return $this->configurator;
 }
 public function isAutowired()
 {
 return $this->autowired;
 }
 public function setAutowired($autowired)
 {
 $this->changes['autowired'] = \true;
 $this->autowired = (bool) $autowired;
 return $this;
 }
 public function getBindings()
 {
 return $this->bindings;
 }
 public function setBindings(array $bindings)
 {
 foreach ($bindings as $key => $binding) {
 if (0 < \strpos($key, '$') && $key !== ($k = \preg_replace('/[ \\t]*\\$/', ' $', $key))) {
 unset($bindings[$key]);
 $bindings[$key = $k] = $binding;
 }
 if (!$binding instanceof BoundArgument) {
 $bindings[$key] = new BoundArgument($binding);
 }
 }
 $this->bindings = $bindings;
 return $this;
 }
 public function addError($error)
 {
 if ($error instanceof self) {
 $this->errors = \array_merge($this->errors, $error->errors);
 } else {
 $this->errors[] = $error;
 }
 return $this;
 }
 public function getErrors()
 {
 foreach ($this->errors as $i => $error) {
 if ($error instanceof \Closure) {
 $this->errors[$i] = (string) $error();
 } elseif (!\is_string($error)) {
 $this->errors[$i] = (string) $error;
 }
 }
 return $this->errors;
 }
 public function hasErrors() : bool
 {
 return (bool) $this->errors;
 }
}
