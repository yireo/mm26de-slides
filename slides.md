<!-- .slide: class="title-slide" data-state="title" -->
# Improving Alpine.js Code With Design Patterns

**Jisse Reitsma** · Yireo

Meet Magento DE 2026

---
<!-- .slide: class="speaker-slide" -->
# Jisse Reitsma
- Founder of Yireo
- Creator of Loki Extensions and Loki Checkout
- Board member of Magento Association

---
# Alpine.js
- Lightweight, so great for performance
- Used in Hyvä, Nebula and Loki Theme Kit
- Easy-to-use and therefore often underestimated

---
<!-- .slide: class="section-slide" -->
Part 1 <!-- .element: class="kicker" -->

# Component definitions

---
# Alpine.js terminology
- Component (instance)
- Component definition

```html
<div x-data='{"foo":"bar"}'>
    Foo: <span x-text="foo"></span>
</div>
```

---
# Use `Alpine.data()` for component definitions
- Separation of JS logic and templating
- Easier to move JS-part to some other part of the page
- Because of CSP disallowing `unsafe-eval`

---
# Use `Alpine.data()` for component definitions
Template: <!-- .element: class="label" -->
```html
<div x-data="component1">
    Foo: <span x-text="foo"></span>
</div>
```

Script: <!-- .element: class="label" -->
```js
document.addEventListener('alpine:init', () => {
    Alpine.data('component1', () => {
        return {
            init() {
                console.log("component1 is being initialized")
            },
            foo: 'bar'
        }
    });
});
```

---
# Naming your component
- Make sure your component name is unique
- Do not start with `init`
- Suggestions:
    - `camelCase` or `PascalCase`
    - Include the vendor name
- With Loki Extensions, block name becomes component name

```xml
<block name="loki-checkout.example" />
```

```js
Alpine.data('LokiCheckoutExample', () => ({}));
```

---
# Reusing component definitions
Template: <!-- .element: class="label" -->
```html
<div x-data="component1"></div>
<div x-data="component1"></div>
<div x-data="component1"></div>
```

Script: <!-- .element: class="label" -->
```js
document.addEventListener('alpine:init', () => {
    Alpine.data('component1', () => ({}));
});
```

---
<!-- .slide: class="section-slide" -->
Part 2 <!-- .element: class="kicker" -->

# Component types & partials

---
# Component types
Template: <!-- .element: class="label" -->
```html
<div x-data="component1"></div>
<div x-data="component2"></div>
<div x-data="component3"></div>
```

Script: <!-- .element: class="label" -->
```js
const componentType = {foo: "bar"};
document.addEventListener('alpine:init', () => {
    Alpine.data('component1', () => ({name: "component1", ...componentType}));
    Alpine.data('component2', () => ({name: "component2", ...componentType}));
    Alpine.data('component3', () => ({name: "component3", ...componentType}));
});
```

---
# Alpine.js terminology
- Component (instance)
- Component definition
- **Component type**
- **Component partial**

---
# Component partials (Loki)
```js
const LokiComponentType = {
    ...LokiLoadDataComponentPartial,
    ...LokiLoadInitMethodsComponentPartial,
    ...LokiLoadDestroyMethodsComponentPartial,
    ...LokiLoaderComponentPartial
};

const LokiFieldComponentType = {
    ...LokiComponentType
};

document.addEventListener('alpine:init', () => {
    Alpine.data('LokiFieldComponent', () => ({
        ...LokiFieldComponentType
    }));
});
```

---
<!-- .slide: class="section-slide" -->
Part 3 <!-- .element: class="kicker" -->

# Sharing state with stores

---
# `Alpine.store()`
```js
document.addEventListener('alpine:init', () => {
    Alpine.store('foobar', {});
});
```

---
<!-- .slide: class="dense" -->
# Components store

```js
Alpine.store('components', {
    components: {},
    componentNames() {
        return Object.keys(this.components);
    },
    add(componentId, component) {
        this.components[componentId] = component;
    },
    get(componentId) {
        return this.components[componentId];
    }
});

Alpine.data('component1', () => {
    return {
        init() {
            Alpine.store('components').add('component1', this);
        },
    }
});
```

```js
Alpine.store('components').componentNames();
```

---
# Component-store-aware component type
```js
const ComponentAwareType = {
    init() {
        Alpine.store('components').add(this.name, this);
    }
};

Alpine.data('component1', () => ({
    ...ComponentAwareType,
    name: 'component1',
}));
```

> `init()` can only be defined once, but you could create an `init()` that calls upon any other method starting with `init*` (like with Loki Components).

---
# Referring from one component to another
```js
Alpine.data('component2', () => {
    return {
        init() {
            Alpine.store('components').add('component2', this);
        },
        foo() {
            return Alpine.store('components').get('component1')?.foo;
        }
    }
});
```

> Note that `component2:foo` is already reactive. Any change of `component1:foo` leads to a change of `component2:foo` (without `$watch` magic).

---
# Magento KnockoutJS / UiComponent magic
- ✅ `import`: done
- ⏱️ `export`: maybe
- <span class="cross">✗</span> `link` (combination of `import` and `export`): nah

---
# Adding a new prop to an existing component 
```js
Alpine.data('component2', () => {
    return {
        foo() {
            return Alpine.store('components').get('component1')?.foo2;
        }
    }
});
```

And then you need to update the template

---
# Pushing with `Alpine.$data`
```js
Alpine.data('component2', () => {
    return {
        init() {
            setTimeout(() => {
                const element = document.querySelector('[x-data="component1"]');
                const component = Alpine.$data(element);
                component['foo'] = 'testing';
            }, 300);
        },
    }
});
```

> Note that `Alpine.$data()` requires the given component to be already instantiated.

---
<!-- .slide: class="section-slide" -->
Part 4 <!-- .element: class="kicker" -->

# Extending components

---
# Hyvä `initHeader` override
```html
<div id="header" x-data="initHeader"></div>
```

```js
function initHeader() { return {}; }
```

Override the global function: <!-- .element: class="label" -->
```js
window.originalInitHeader = window.initHeader;
window.initHeader = function() {
    const originalData = window.originalInitHeader();
    return {
        ...originalData,
        foo: "bar"
    }
}
```

---
# Hyvä `initHeader` with a `Proxy`
```js
window.initHeader = new Proxy(window.initHeader, {
    apply(target, thisArg, args) {
        console.log('Before original');
        const result = Reflect.apply(target, thisArg, args);
        console.log('After original');
        return result;
    }
});
```

---
# Creating a `Proxy` for `Alpine.data()`
```js
Alpine.data = new Proxy(Alpine.data, {
    apply(target, thisArg, args) {
        const [name, factory] = args;
        console.log('Registering:', name);
        return Reflect.apply(target, thisArg, args);
    }
});
```

---
<!-- .slide: class="dense" -->
# Hooking this proxy to `ComponentExtender`
```js
Alpine.data = new Proxy(Alpine.data, {
    apply(target, thisArg, args) {
        const [name, factory] = args;
        if (typeof factory !== 'function') {
            return Reflect.apply(target, thisArg, args);
        }

        const extendedFactory = new Proxy(factory, {
            apply(target, thisArg, factoryArgs) {
                const component = Reflect.apply(
                    target, thisArg, factoryArgs
                );

                return ComponentExtender.extend(
                    name, component, factoryArgs
                );
            }
        });

        return Reflect.apply(target, thisArg, [
            name,
            extendedFactory
        ]);
    }
});
```

---
# `ComponentExtender` logic
```js
const ComponentExtender = {
    extenders: [],
    addExtender(extenderName, extenderCallback) {
        this.extenders.push({
            name: extenderName,
            callback: extenderCallback,
        });
    },
    extend(componentName, component, args) {
        this.extenders.forEach((extender) => {
            extender.callback(componentName, component, args);
        });

        return component;
    }
};
```

---
# Example extender
```js
ComponentExtender.addExtender(
    'component1Extender',
    (componentName, component, args) => {
        if (componentName !== 'component1') {
            return;
        }

        component.foo = 'component1-override';
        component.customProperty = 'Hello World';
    }
);
```

---
# Ideas
- ✅ Select all with prop X (like invalid field components in Loki Checkout)
- <span class="cross">✗</span> Automatically add any Alpine.js component to a global store
- ⏱️ Proxy specific setters to make them more secure
- ⏱️ Alpine store with services for usage in components (service container)

---
# Loki Checkout
- <!-- .element: class="fragment" data-fragment-index="1" --> Fastest checkout under Luma, Hyvä and Nebula 
- <!-- .element: class="fragment" data-fragment-index="2" --> 299 modules with all tests in the green
- <!-- .element: class="fragment" data-fragment-index="3" --> Exciting new B2B Loki Extensions for 2027
- <!-- .element: class="fragment" data-fragment-index="4" --> Working towards a free plan (Q1 2027)

---
<!-- .slide: class="title-slide" data-state="title" -->
# Questions

---
<!-- .slide: class="title-slide" data-state="title" -->
# Thank you!

**Jisse Reitsma** · Yireo
