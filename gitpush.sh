#!/bin/sh

while true; do
  git pull && break
  sleep 60
done

while true; do
  git push && break
  sleep 60
done
