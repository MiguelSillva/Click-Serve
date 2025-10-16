<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Nome Restaurante - Mesas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <style>
    body, html {
      max-width: 420px;
      margin: 0 auto;
      height: 100vh;
      background: white;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
        Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
    }
    .table-icon {
      width: 40px;
      height: 40px;
      border-radius: 0.5rem;
      object-fit: cover;
    }
    .list-group-item {
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
      border: 1px solid #d1d5db;
      cursor: pointer;
    }
    .nav-tabs .nav-link {
      font-weight: 600;
      font-size: 0.75rem;
      color: #4b5563;
    }
    .nav-tabs .nav-link.active {
      border-color: transparent transparent #000000;
      color: #000000;
      font-weight: 700;
    }
    .scrollable-list {
      max-height: calc(100vh - 140px);
      overflow-y: auto;
      padding-right: 0.25rem;
    }
    /* Custom scrollbar for WebKit */
    .scrollable-list::-webkit-scrollbar {
      width: 6px;
    }
    .scrollable-list::-webkit-scrollbar-track {
      background: transparent;
    }
    .scrollable-list::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="container py-3 d-flex flex-column h-100">
    <header class="d-flex align-items-center justify-content-between mb-2">
      <button type="button" class="btn btn-link p-0 text-dark" aria-label="Buscar">
        <i class="fas fa-search fa-lg"></i>
      </button>
      <h1 class="fw-bold fs-5 m-0">Nome Restaurante</h1>
      <img src="https://storage.googleapis.com/a1aa/image/76e698d5-fbf6-4ef0-aa9c-ab2600b27413.jpg" alt="Logo red circle with yellow text 'CLICK &amp; SERVE!' inside" class="rounded-circle" width="40" height="40" loading="lazy" />
    </header>
    <ul class="nav nav-tabs mb-2" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="ocupadas-tab" data-bs-toggle="tab" data-bs-target="#ocupadas" type="button" role="tab" aria-controls="ocupadas" aria-selected="true">Ocupadas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="disponiveis-tab" data-bs-toggle="tab" data-bs-target="#disponiveis" type="button" role="tab" aria-controls="disponiveis" aria-selected="false">Disponíveis</button>
      </li>
    </ul>
    <div class="tab-content flex-grow-1 scrollable-list" id="myTabContent">
      <div class="tab-pane fade show active" id="ocupadas" role="tabpanel" aria-labelledby="ocupadas-tab" tabindex="0">
        <ul class="list-group">
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/70b5861f-1c69-4b16-e69c-db7628a644b1.jpg" alt="Green icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 1</span>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/577a706b-b359-4a02-13c4-cffbd9d596d1.jpg" alt="Red icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 3</span>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/70b5861f-1c69-4b16-e69c-db7628a644b1.jpg" alt="Green icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 4</span>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/577a706b-b359-4a02-13c4-cffbd9d596d1.jpg" alt="Red icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 6</span>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/70b5861f-1c69-4b16-e69c-db7628a644b1.jpg" alt="Green icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 7</span>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/70b5861f-1c69-4b16-e69c-db7628a644b1.jpg" alt="Green icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 8</span>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://storage.googleapis.com/a1aa/image/577a706b-b359-4a02-13c4-cffbd9d596d1.jpg" alt="Red icon of table with two chairs" class="table-icon" />
            <span class="ms-3 fw-semibold fs-6">Mesa 11</span>
          </li>
        </ul>
      </div>
      <div class="tab-pane fade" id="disponiveis" role="tabpanel" aria-labelledby="disponiveis-tab" tabindex="0">
        <!-- Empty or add available tables here if needed -->
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>