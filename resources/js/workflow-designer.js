import cytoscape from 'cytoscape';
import dagre from 'cytoscape-dagre';

cytoscape.use(dagre);
window.cytoscape = cytoscape;

const STEP_TYPE_COLORS = {
    start: { bg: '#d1fae5', border: '#10b981', text: '#065f46' },
    end: { bg: '#fee2e2', border: '#ef4444', text: '#7f1d1d' },
    approval: { bg: '#dbeafe', border: '#3b82f6', text: '#1e3a8a' },
    task: { bg: '#f3f4f6', border: '#6b7280', text: '#111827' },
    gateway: { bg: '#ede9fe', border: '#8b5cf6', text: '#4c1d95' },
};

function buildCytoscapeElements(steps, transitions) {
    const nodes = steps.map(step => {
        const colors = STEP_TYPE_COLORS[step.step_type] || STEP_TYPE_COLORS.task;
        return {
            data: {
                id: step.uuid,
                label: `${step.name}\n[${step.step_type}]`,
                bg: colors.bg,
                border: colors.border,
                textColor: colors.text,
                stepData: step,
            },
            position: step.canvas_position ? { x: step.canvas_position.x, y: step.canvas_position.y } : undefined,
        };
    });

    const edges = transitions
        .filter(t => t.from_uuid && t.to_uuid)
        .map((t, i) => ({
            data: {
                id: `edge-${i}`,
                source: t.from_uuid,
                target: t.to_uuid,
                label: t.action_name || '',
            },
        }));

    return [...nodes, ...edges];
}

function initCanvas(containerEl, wire, initialSteps, initialTransitions) {
    const cy = cytoscape({
        container: containerEl,
        elements: buildCytoscapeElements(initialSteps, initialTransitions),
        style: [
            {
                selector: 'node',
                style: {
                    'background-color': 'data(bg)',
                    'border-color': 'data(border)',
                    'border-width': 2,
                    'color': 'data(textColor)',
                    'label': 'data(label)',
                    'text-valign': 'center',
                    'text-halign': 'center',
                    'text-wrap': 'wrap',
                    'text-max-width': '120px',
                    'font-size': '11px',
                    'width': 140,
                    'height': 60,
                    'shape': 'roundrectangle',
                    'padding': '8px',
                },
            },
            {
                selector: 'node:selected',
                style: {
                    'border-width': 3,
                    'border-color': '#6366f1',
                    'overlay-color': '#6366f1',
                    'overlay-opacity': 0.1,
                },
            },
            {
                selector: 'edge',
                style: {
                    'width': 2,
                    'line-color': '#94a3b8',
                    'target-arrow-color': '#94a3b8',
                    'target-arrow-shape': 'triangle',
                    'curve-style': 'bezier',
                    'label': 'data(label)',
                    'font-size': '10px',
                    'color': '#64748b',
                    'text-background-color': '#fff',
                    'text-background-opacity': 0.8,
                    'text-background-padding': '2px',
                },
            },
        ],
        layout: { name: 'preset' },
        userZoomingEnabled: true,
        userPanningEnabled: true,
        boxSelectionEnabled: false,
    });

    // Auto-layout nodes that have no canvas_position
    const unpositioned = cy.nodes().filter(n => !n.data('stepData')?.canvas_position);
    if (unpositioned.length > 0) {
        cy.layout({
            name: 'dagre',
            rankDir: 'LR',
            nodeSep: 60,
            rankSep: 120,
            padding: 40,
            animate: false,
        }).run();

        // Persist auto-layout positions back to Livewire
        cy.nodes().forEach(node => {
            const pos = node.position();
            wire.updateStepPosition(node.id(), pos.x, pos.y);
        });
    }

    // Persist drag position to Livewire
    cy.on('dragfree', 'node', event => {
        const node = event.target;
        const pos = node.position();
        wire.updateStepPosition(node.id(), pos.x, pos.y);
    });

    // Select step on click
    cy.on('tap', 'node', event => {
        const node = event.target;
        wire.selectStep(node.id());
    });

    return cy;
}

// Alpine component for the canvas
window.workflowCanvas = function (wireId) {
    return {
        cy: null,

        init() {
            const containerEl = this.$refs.cytoscapeContainer;
            if (!containerEl) return;

            const wire = window.Livewire?.find(wireId);
            if (!wire) return;

            const steps = wire.get('steps') || [];
            const transitions = wire.get('transitions') || [];

            this.cy = initCanvas(containerEl, wire, steps, transitions);
        },

        refreshGraph(steps, transitions) {
            if (!this.cy) return;
            this.cy.elements().remove();
            this.cy.add(buildCytoscapeElements(steps, transitions));

            // Re-layout only unpositioned nodes
            const unpositioned = this.cy.nodes().filter(n => !n.data('stepData')?.canvas_position);
            if (unpositioned.length > 0) {
                this.cy.layout({ name: 'dagre', rankDir: 'LR', nodeSep: 60, rankSep: 120, padding: 40 }).run();
            }
        },
    };
};
