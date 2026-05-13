@php
    $scannerId = $scannerId ?? 'scannerUpload';
    $buttonLabel = $buttonLabel ?? 'Scan Document';
    $description = $description ?? 'Choose a connected scanner, scan the document, and APM will save it directly to this record.';
    $modalTitle = $modalTitle ?? 'Scan Document';
    $documentLabel = $documentLabel ?? 'document';
@endphp

<div class="border rounded-3 bg-light p-3 mb-3 no-print">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <div class="fw-semibold">Scan inside APM</div>
            <div class="text-muted small">{{ $description }}</div>
        </div>
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#{{ $scannerId }}Modal">
            <i class="bi bi-printer me-1"></i> {{ $buttonLabel }}
        </button>
    </div>
</div>

<div class="modal fade" id="{{ $scannerId }}Modal" tabindex="-1" aria-labelledby="{{ $scannerId }}ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="{{ $scannerId }}ModalLabel">{{ $modalTitle }}</h5>
                    <div class="text-muted small">The scan will be uploaded directly to APM after scanning.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="{{ $scannerId }}Status" class="alert alert-info mb-3">
                    Looking for scanners connected to this computer...
                </div>
                <label class="form-label fw-semibold" for="{{ $scannerId }}Select">Scanner Selection</label>
                <select class="form-select" id="{{ $scannerId }}Select" disabled>
                    <option value="">Loading scanners...</option>
                </select>
                <div class="form-text">
                    This requires the APM Scanner Bridge to be installed and running on this computer.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" id="{{ $scannerId }}Refresh">
                    Refresh Scanners
                </button>
                <button class="btn btn-primary" type="button" id="{{ $scannerId }}Start" disabled>
                    Scan and Upload
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const modal = document.getElementById(@json($scannerId . 'Modal'));
            const status = document.getElementById(@json($scannerId . 'Status'));
            const scannerSelect = document.getElementById(@json($scannerId . 'Select'));
            const refreshButton = document.getElementById(@json($scannerId . 'Refresh'));
            const startButton = document.getElementById(@json($scannerId . 'Start'));
            const uploadUrl = @json($uploadUrl);
            const csrfToken = @json(csrf_token());
            const documentLabel = @json($documentLabel);
            const bridgeUrls = ['http://127.0.0.1:17654', 'http://localhost:17654'];
            let activeBridgeUrl = null;

            const setStatus = (type, message) => {
                status.className = `alert alert-${type} mb-3`;
                status.textContent = message;
            };

            const scannerName = (scanner) => scanner.name || scanner.label || scanner.id || scanner.deviceId || 'Scanner';
            const scannerIdValue = (scanner) => scanner.id || scanner.deviceId || scanner.name || scanner.label;

            const extensionForMime = (mime) => {
                if (mime === 'image/jpeg') return 'jpg';
                if (mime === 'image/png') return 'png';
                if (mime === 'image/webp') return 'webp';
                if (mime === 'image/bmp') return 'bmp';
                if (mime === 'image/tiff') return 'tiff';
                return 'pdf';
            };

            const fetchBridge = async (path, options = {}) => {
                const urls = activeBridgeUrl ? [activeBridgeUrl] : bridgeUrls;

                for (const url of urls) {
                    try {
                        const response = await fetch(`${url}${path}`, {
                            cache: 'no-store',
                            ...options,
                        });

                        if (response.ok) {
                            activeBridgeUrl = url;
                            return response;
                        }
                    } catch (error) {
                        // Try the next localhost variant before showing the user an error.
                    }
                }

                throw new Error('APM Scanner Bridge was not detected on this computer.');
            };

            const loadScanners = async () => {
                scannerSelect.disabled = true;
                startButton.disabled = true;
                scannerSelect.innerHTML = '<option value="">Loading scanners...</option>';
                setStatus('info', 'Looking for scanners connected to this computer...');

                try {
                    const response = await fetchBridge('/scanners');
                    const payload = await response.json();
                    const scanners = Array.isArray(payload) ? payload : (payload.scanners || []);

                    scannerSelect.innerHTML = '';
                    if (!scanners.length) {
                        scannerSelect.innerHTML = '<option value="">No scanners found</option>';
                        setStatus('warning', 'No scanners were found. Please check if the scanner is connected and turned on.');
                        return;
                    }

                    scanners.forEach((scanner) => {
                        const option = document.createElement('option');
                        option.value = scannerIdValue(scanner);
                        option.textContent = scannerName(scanner);
                        scannerSelect.appendChild(option);
                    });

                    scannerSelect.disabled = false;
                    startButton.disabled = false;
                    setStatus('success', `Found ${scanners.length} scanner${scanners.length === 1 ? '' : 's'}. Choose one, then click Scan and Upload.`);
                } catch (error) {
                    scannerSelect.innerHTML = '<option value="">Scanner bridge not running</option>';
                    setStatus('warning', 'Scanner bridge not detected. Please run the APM Scanner Bridge on this computer, then refresh scanners.');
                }
            };

            const uploadScan = async (blob) => {
                const extension = extensionForMime(blob.type);
                const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
                const filename = `scan-${documentLabel.replace(/[^A-Za-z0-9_-]+/g, '-')}-${timestamp}.${extension}`;
                const formData = new FormData();

                formData.append('_token', csrfToken);
                formData.append('attachments[]', blob, filename);

                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok && !response.redirected) {
                    throw new Error('APM could not save the scanned document.');
                }

                window.location.reload();
            };

            const scanAndUpload = async () => {
                if (!scannerSelect.value) {
                    setStatus('warning', 'Please choose a scanner first.');
                    return;
                }

                startButton.disabled = true;
                refreshButton.disabled = true;
                setStatus('info', 'Scanning now. Please wait until the document is uploaded to APM...');

                try {
                    const response = await fetchBridge('/scan', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            scanner_id: scannerSelect.value,
                            format: 'pdf',
                        }),
                    });
                    const blob = await response.blob();

                    if (!blob.size) {
                        throw new Error('The scanner returned an empty file.');
                    }

                    await uploadScan(blob);
                } catch (error) {
                    setStatus('danger', error.message || 'Scanning failed. Please try again or use manual upload.');
                    startButton.disabled = false;
                    refreshButton.disabled = false;
                }
            };

            modal?.addEventListener('shown.bs.modal', loadScanners);
            refreshButton?.addEventListener('click', loadScanners);
            startButton?.addEventListener('click', scanAndUpload);
        })();
    </script>
@endpush
