const ContainerRoleDefinitions = {
    layout: {
        label: 'Layout',
        defaults: {
            containerRole: 'layout',
            display: 'block',
            gridSpan: 12,
            gridColumns: 12,
            gap: 0,
            justifyContent: 'flex-start',
            alignItems: 'stretch',
            flexDirection: 'row',
            maxWidth: 1200,
            paddingTop: 0,
            paddingBottom: 0,
            paddingLeft: 24,
            paddingRight: 24
        }
    },
    grid: {
        label: 'Grid',
        defaults: {
            containerRole: 'grid',
            display: 'grid',
            gridSpan: 12,
            gridColumns: 12,
            gap: 24,
            justifyContent: 'flex-start',
            alignItems: 'stretch',
            flexDirection: 'row',
            maxWidth: '100%',
            paddingTop: 0,
            paddingBottom: 0,
            paddingLeft: 0,
            paddingRight: 0
        }
    },
    'grid-item': {
        label: 'Grid Item',
        defaults: {
            containerRole: 'grid-item',
            display: 'block',
            gridSpan: 6,
            gridColumns: 12,
            gap: 0,
            justifyContent: 'flex-start',
            alignItems: 'stretch',
            flexDirection: 'row',
            maxWidth: '100%',
            paddingTop: 0,
            paddingBottom: 0,
            paddingLeft: 0,
            paddingRight: 0
        }
    },
    card: {
        label: 'Card',
        defaults: {
            containerRole: 'card',
            display: 'block',
            gridSpan: 4,
            gridColumns: 12,
            gap: 0,
            justifyContent: 'flex-start',
            alignItems: 'stretch',
            flexDirection: 'row',
            maxWidth: '100%',
            paddingTop: 24,
            paddingBottom: 24,
            paddingLeft: 24,
            paddingRight: 24,
            borderStyle: 'solid',
            borderWidth: 1
        }
    },
    content: {
        label: 'Content',
        defaults: {
            containerRole: 'content',
            display: 'flex',
            gridSpan: 6,
            gridColumns: 12,
            gap: 16,
            justifyContent: 'flex-start',
            alignItems: 'stretch',
            flexDirection: 'column',
            maxWidth: 720,
            paddingTop: 0,
            paddingBottom: 0,
            paddingLeft: 0,
            paddingRight: 0
        }
    },
    group: {
        label: 'Group',
        defaults: {
            containerRole: 'group',
            display: 'block',
            gridSpan: 12,
            gridColumns: 12,
            gap: 0,
            justifyContent: 'flex-start',
            alignItems: 'stretch',
            flexDirection: 'row',
            maxWidth: '100%',
            paddingTop: 0,
            paddingBottom: 0,
            paddingLeft: 0,
            paddingRight: 0
        }
    }
};

const ContainerRoleValues = Object.keys(ContainerRoleDefinitions);
const ContainerRolePresetVersion = 1;

function isPlainObject(value) {
    return (
        value !== null
        &&
        typeof value === 'object'
        &&
        !Array.isArray(value)
        &&
        Object.prototype.toString.call(value) === '[object Object]'
    );
}

function positiveNumber(value) {
    const number = Number.parseFloat(value);

    return Number.isFinite(number) && number > 0;
}

function hasCardSurface(props) {
    return (
        (typeof props.backgroundColor === 'string'
            && props.backgroundColor !== ''
            && props.backgroundColor !== 'transparent')
        || positiveNumber(props.borderWidth)
        || positiveNumber(props.borderRadius)
        || positiveNumber(props.padding)
        || positiveNumber(props.paddingTop)
        || positiveNumber(props.paddingBottom)
        || positiveNumber(props.paddingLeft)
        || positiveNumber(props.paddingRight)
    );
}

function hasContentChildren(node) {
    const children = Array.isArray(node?.children)
        ? node.children
        : [];

    return children.some(child => [
        'text',
        'richtext',
        'heading',
        'button',
        'image'
    ].includes(child?.type));
}

function presetPropsForRole(role) {
    const props = {};

    Object.entries(ContainerRoleDefinitions[role].defaults)
        .forEach(([key, value]) => {
            if (key !== 'containerRole') {
                props[key] = value;
            }
        });

    return props;
}

function rolePresetFor(value) {
    const role = typeof value === 'string' && ContainerRoleDefinitions[value]
        ? value
        : 'group';

    return {
        role,
        version: ContainerRolePresetVersion,
        applied: presetPropsForRole(role)
    };
}

function normalizeRolePreset(value) {
    if (
        !isPlainObject(value)
        ||
        typeof value.role !== 'string'
        ||
        !ContainerRoleDefinitions[value.role]
        ||
        !isPlainObject(value.applied)
    ) {
        return null;
    }

    return value;
}

function isMissingValue(value) {
    return value === undefined || value === null || value === '';
}

function isPlainNumberLike(value) {
    if (typeof value === 'number') {
        return Number.isFinite(value);
    }

    return (
        typeof value === 'string'
        &&
        /^-?(?:\d+(?:\.\d+)?|\.\d+)$/.test(value.trim())
    );
}

function valuesMatch(left, right) {
    if (isMissingValue(left) && isMissingValue(right)) {
        return true;
    }

    if (isPlainNumberLike(left) && isPlainNumberLike(right)) {
        return Number(left) === Number(right);
    }

    return String(left).trim().toLowerCase()
        === String(right).trim().toLowerCase();
}

function shouldApplyPresetValue(currentValue, targetValue, key, previousApplied, previousDefaults) {
    if (isMissingValue(currentValue)) {
        return true;
    }

    if (valuesMatch(currentValue, targetValue)) {
        return true;
    }

    if (
        previousApplied
        &&
        Object.prototype.hasOwnProperty.call(previousApplied, key)
        &&
        valuesMatch(currentValue, previousApplied[key])
    ) {
        return true;
    }

    return Boolean(
        previousDefaults
        &&
        Object.prototype.hasOwnProperty.call(previousDefaults, key)
        &&
        valuesMatch(currentValue, previousDefaults[key])
    );
}

window.BuilderContainerRoles = {
    values() {
        return [...ContainerRoleValues];
    },

    options() {
        return ContainerRoleValues.map(value => [
            value,
            ContainerRoleDefinitions[value].label
        ]);
    },

    normalize(value, fallback = 'group') {
        return typeof value === 'string' && ContainerRoleDefinitions[value]
            ? value
            : fallback;
    },

    label(value) {
        const role = this.normalize(value);

        return ContainerRoleDefinitions[role].label;
    },

    defaults(value = 'group') {
        const role = this.normalize(value);

        return {
            ...ContainerRoleDefinitions[role].defaults
        };
    },

    presetProps(value = 'group') {
        return {
            ...presetPropsForRole(this.normalize(value))
        };
    },

    presetSnapshot(value = 'group') {
        return rolePresetFor(this.normalize(value));
    },

    applyCreationDefaults(value = 'group', props = {}) {
        const role = this.normalize(value);
        const safeProps = isPlainObject(props) ? props : {};
        const baseProps = {
            ...safeProps
        };
        const sourceRole = this.normalize(safeProps.containerRole, 'group');
        const sourcePreset = normalizeRolePreset(safeProps.rolePreset);

        if (role !== sourceRole && sourcePreset?.role === sourceRole) {
            Object.entries(presetPropsForRole(sourceRole))
                .forEach(([key, sourceValue]) => {
                    if (valuesMatch(baseProps[key], sourceValue)) {
                        delete baseProps[key];
                    }
                });
        }

        return {
            ...this.defaults(role),
            ...baseProps,
            containerRole: role,
            rolePreset: sourcePreset?.role === role
                ? sourcePreset
                : rolePresetFor(role)
        };
    },

    prepareRoleChange(props = {}) {
        const safeProps = isPlainObject(props) ? props : {};
        const currentRole = this.normalize(safeProps.containerRole, 'group');

        if (normalizeRolePreset(safeProps.rolePreset)) {
            return safeProps;
        }

        safeProps.rolePreset = rolePresetFor(currentRole);

        return safeProps;
    },

    applyPreset(value = 'group', props = {}) {
        const role = this.normalize(value);
        const safeProps = isPlainObject(props) ? props : {};
        const existingPreset = normalizeRolePreset(safeProps.rolePreset);
        const previousRole = existingPreset
            ? this.normalize(existingPreset.role)
            : this.normalize(safeProps.containerRole, 'group');
        const previousApplied = existingPreset?.applied || null;
        const previousDefaults = existingPreset
            ? null
            : presetPropsForRole(previousRole);
        const targetDefaults = presetPropsForRole(role);
        const nextProps = {
            ...safeProps,
            containerRole: role
        };
        const applied = {};
        const appliedKeys = [];
        const preservedKeys = [];

        Object.entries(targetDefaults).forEach(([key, targetValue]) => {
            const currentValue = safeProps[key];

            if (
                shouldApplyPresetValue(
                    currentValue,
                    targetValue,
                    key,
                    previousApplied,
                    previousDefaults
                )
            ) {
                nextProps[key] = targetValue;
                applied[key] = targetValue;
                appliedKeys.push(key);
                return;
            }

            preservedKeys.push(key);
        });

        nextProps.rolePreset = {
            role,
            version: ContainerRolePresetVersion,
            applied
        };

        return {
            props: nextProps,
            appliedKeys,
            preservedKeys
        };
    },

    presetStatus(props = {}) {
        const safeProps = isPlainObject(props) ? props : {};
        const role = this.normalize(safeProps.containerRole, 'group');
        const label = this.label(role);
        const targetDefaults = presetPropsForRole(role);
        const keys = Object.keys(targetDefaults);
        const preset = normalizeRolePreset(safeProps.rolePreset);
        const matchingKeys = keys.filter(key =>
            valuesMatch(safeProps[key], targetDefaults[key])
        );
        const appliedKeys = preset?.role === role
            ? Object.keys(preset.applied || {})
            : [];

        if (matchingKeys.length === keys.length) {
            return {
                state: 'applied',
                label: `${label} preset applied`,
                description: 'This container already matches the recommended role geometry.',
                buttonLabel: `Apply ${label} Preset`,
                disabled: true
            };
        }

        if (appliedKeys.length > 0 && preset?.role === role) {
            return {
                state: 'partial',
                label: `${label} preset partially applied`,
                description: 'Customized values are preserved. Apply again to fill only safe fields.',
                buttonLabel: `Apply ${label} Preset`,
                disabled: false
            };
        }

        return {
            state: 'available',
            label: `Recommended ${label} preset available`,
            description: 'Apply the preset to configure safe missing/default layout fields.',
            buttonLabel: `Apply ${label} Preset`,
            disabled: false
        };
    },

    infer(node, parent = null, siblingIndex = 0) {
        if (node?.type !== 'container') {
            return null;
        }

        const props = isPlainObject(node.props)
            ? node.props
            : {};

        if (
            typeof props.containerRole === 'string'
            &&
            ContainerRoleDefinitions[props.containerRole]
        ) {
            return props.containerRole;
        }

        const display = props.display || 'block';
        const parentDisplay = parent?.props?.display || 'block';

        if (display === 'grid') {
            return 'grid';
        }

        if (parent?.type === 'container' && parentDisplay === 'grid') {
            return 'grid-item';
        }

        if (parent?.type === 'section' && siblingIndex === 0) {
            return 'layout';
        }

        if (hasCardSurface(props)) {
            return 'card';
        }

        if (hasContentChildren(node)) {
            return 'content';
        }

        return 'group';
    }
};
