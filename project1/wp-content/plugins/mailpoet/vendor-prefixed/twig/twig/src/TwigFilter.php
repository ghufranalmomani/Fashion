<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Expression\FilterExpression;
use MailPoetVendor\Twig\Node\Node;
class TwigFilter
{
 private $name;
 private $callable;
 private $options;
 private $arguments = [];
 public function __construct(string $name, $callable = null, array $options = [])
 {
 if (__CLASS__ !== static::class) {
 @\trigger_error('Overriding ' . __CLASS__ . ' is deprecated since Twig 2.4.0 and the class will be final in 3.0.', \E_USER_DEPRECATED);
 }
 $this->name = $name;
 $this->callable = $callable;
 $this->options = \array_merge(['needs_environment' => \false, 'needs_context' => \false, 'is_variadic' => \false, 'is_safe' => null, 'is_safe_callback' => null, 'pre_escape' => null, 'preserves_safety' => null, 'node_class' => FilterExpression::class, 'deprecated' => \false, 'alternative' => null], $options);
 }
 public function getName()
 {
 return $this->name;
 }
 public function getCallable()
 {
 return $this->callable;
 }
 public function getNodeClass()
 {
 return $this->options['node_class'];
 }
 public function setArguments($arguments)
 {
 $this->arguments = $arguments;
 }
 public function getArguments()
 {
 return $this->arguments;
 }
 public function needsEnvironment()
 {
 return $this->options['needs_environment'];
 }
 public function needsContext()
 {
 return $this->options['needs_context'];
 }
 public function getSafe(Node $filterArgs)
 {
 if (null !== $this->options['is_safe']) {
 return $this->options['is_safe'];
 }
 if (null !== $this->options['is_safe_callback']) {
 return $this->options['is_safe_callback']($filterArgs);
 }
 }
 public function getPreservesSafety()
 {
 return $this->options['preserves_safety'];
 }
 public function getPreEscape()
 {
 return $this->options['pre_escape'];
 }
 public function isVariadic()
 {
 return $this->options['is_variadic'];
 }
 public function isDeprecated()
 {
 return (bool) $this->options['deprecated'];
 }
 public function getDeprecatedVersion()
 {
 return $this->options['deprecated'];
 }
 public function getAlternative()
 {
 return $this->options['alternative'];
 }
}
// For Twig 1.x compatibility
\class_alias('MailPoetVendor\\Twig\\TwigFilter', 'MailPoetVendor\\Twig_SimpleFilter', \false);
\class_alias('MailPoetVendor\\Twig\\TwigFilter', 'MailPoetVendor\\Twig_Filter');
// Ensure that the aliased name is loaded to keep BC for classes implementing the typehint with the old aliased name.
\class_exists('MailPoetVendor\\Twig\\Node\\Node');
