#!/bin/sh
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
for phase in \
  phase-01-core-and-assets \
  phase-02-custom-admin-dashboard \
  phase-03-cpt-and-legacy-meta \
  phase-04-header-footer-navigation \
  phase-05-homepage-and-sections \
  phase-06-woocommerce-and-products \
  phase-07-projects-blog-and-pages \
  phase-08-ajax-filters-and-audit
do
  "$ROOT/temp/$phase/tests/run-phase-tests.sh"
done
printf "All phase test runners completed.\n"
