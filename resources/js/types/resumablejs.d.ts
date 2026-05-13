// Augment the bundled resumablejs types to add options that exist at runtime
// but are missing from node_modules/resumablejs/resumable.d.ts.
declare namespace Resumable {
    interface ConfigurationHash {
        testTarget?: string | null;
        throttleProgressCallbacks?: number;
    }
}
