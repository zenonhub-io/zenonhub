# Build Stage
FROM golang:1.20-alpine AS builder

# Install build dependencies
RUN apk add --no-cache make gcc musl-dev git

# Set working directory
WORKDIR /app

# Clone the go-zenon repository
RUN git clone https://github.com/zenon-network/go-zenon.git .

# Download dependencies
RUN go mod download

# Build znnd using Makefile
RUN make znnd

# Final Stage
FROM alpine:latest

# Install runtime dependencies
RUN apk add --no-cache ca-certificates

# Set working directory
WORKDIR /root

# Create the data directory
RUN mkdir -p /root/.znn

# Copy the binary from the builder stage
COPY --from=builder /app/build/znnd /usr/local/bin/znnd

# Copy default config
COPY znnd-config.json /root/config.json.default

# Copy entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose ports:
# 35995: P2P
# 35997: HTTP RPC
# 35998: WS RPC
EXPOSE 35995 35997 35998

# Entry point
ENTRYPOINT ["/entrypoint.sh"]
