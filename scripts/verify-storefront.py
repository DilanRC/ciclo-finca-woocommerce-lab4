#!/usr/bin/env python3
"""Smoke check that the editable WooCommerce catalog renders on its archives."""

from argparse import ArgumentParser
from html.parser import HTMLParser
from urllib.request import urlopen


class StorefrontSummary(HTMLParser):
    def __init__(self):
        super().__init__()
        self.headings = []
        self.product_categories = []
        self._reading_h1 = False
        self._heading_parts = []

    def handle_starttag(self, tag, attrs):
        attributes = dict(attrs)
        classes = attributes.get("class", "").split()
        if tag == "h1":
            self._reading_h1 = True
            self._heading_parts = []
        if tag == "li" and "wc-block-product" in classes:
            self.product_categories.append(
                {value for value in classes if value.startswith("product_cat-")}
            )

    def handle_data(self, data):
        if self._reading_h1:
            self._heading_parts.append(data)

    def handle_endtag(self, tag):
        if tag == "h1" and self._reading_h1:
            self.headings.append("".join(self._heading_parts).strip())
            self._reading_h1 = False


def fetch_summary(base_url, path):
    url = f"{base_url.rstrip('/')}/{path.lstrip('/')}"
    with urlopen(url, timeout=20) as response:
        if response.status != 200:
            raise AssertionError(f"{url} devolvió HTTP {response.status}")
        html = response.read().decode("utf-8", errors="replace")

    summary = StorefrontSummary()
    summary.feed(html)
    return summary


def main():
    parser = ArgumentParser(description=__doc__)
    parser.add_argument("base_url", nargs="?", default="http://localhost:8094")
    args = parser.parse_args()

    shop = fetch_summary(args.base_url, "/shop/")
    if len(shop.headings) != 1 or not shop.headings[0]:
        raise AssertionError(f"/shop/ debe mostrar un H1 editable; recibió {shop.headings!r}")
    if len(shop.product_categories) < 6:
        raise AssertionError(f"/shop/ debe mostrar al menos 6 productos; recibió {len(shop.product_categories)}")

    category = fetch_summary(args.base_url, "/product-category/bicicletas/")
    if len(category.headings) != 1 or not category.headings[0]:
        raise AssertionError(f"La categoría debe mostrar su H1 dinámico; recibió {category.headings!r}")
    if not category.product_categories:
        raise AssertionError("La categoría Bicicletas no mostró productos")
    if any("product_cat-bicicletas" not in classes for classes in category.product_categories):
        raise AssertionError("La categoría Bicicletas mostró productos ajenos a esa categoría")

    print(f"PASS /shop/: un H1 editable y {len(shop.product_categories)} tarjetas de producto")
    print(f"PASS /product-category/bicicletas/: un H1 dinámico y {len(category.product_categories)} tarjetas de la categoría")


if __name__ == "__main__":
    main()
